
<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Reviewer_model extends CI_Model
{
    private $mock_file;
    private $assignment_file;

    public function __construct()
    {
        parent::__construct();

        $this->mock_file = FCPATH . 'data/mock_openalex_data.json';
        $this->assignment_file = FCPATH . 'storage/assignments.json';
    }

    // Read mock data from JSON file
    private function read_mock_data()
    {
        if (!file_exists($this->mock_file)) {
            return false;
        }

        $data = json_decode(file_get_contents($this->mock_file), true);

        return is_array($data) ? $data : false;
    }

    // Get all articles
    public function get_articles()
    {
        $data = $this->read_mock_data();

        return ($data !== false && isset($data['articles']))
            ? $data['articles']
            : false;
    }

    // Get all reviewers
    public function get_reviewers()
    {
        $data = $this->read_mock_data();

        return ($data !== false && isset($data['reviewers']))
            ? $data['reviewers']
            : false;
    }

    // Get existing assignments
    private function get_assignments()
    {
        if (!file_exists($this->assignment_file)) {

            // Create storage directory if it doesn't exist
            $directory = dirname($this->assignment_file);

            if (!is_dir($directory)) {
                mkdir($directory, 0755, true);
            }

            // Create assignments file with empty array
            file_put_contents($this->assignment_file, '[]');

            return [];
        }

        $data = json_decode(file_get_contents($this->assignment_file), true);

        return is_array($data) ? $data : false;
    }
    // Get all saved assignments
    public function get_all_assignments()
    {
        return $this->get_assignments();
    }
    // Assign reviewer to article
    public function assign_reviewer($article_id, $reviewer_id)
    {
        $articles = $this->get_articles();
        $reviewers = $this->get_reviewers();

        if ($articles === false || $reviewers === false) {
            return [
                'success' => false,
                'message' => 'Unable to read mock data.'
            ];
        }

        $article = null;
        $reviewer = null;

        // Find selected article
        foreach ($articles as $item) {
            if ($item['id'] === $article_id) {
                $article = $item;
                break;
            }
        }

        // Find selected reviewer
        foreach ($reviewers as $item) {
            if ($item['id'] === $reviewer_id) {
                $reviewer = $item;
                break;
            }
        }

        // Check article
        if ($article === null) {
            return [
                'success' => false,
                'message' => 'Article not found.'
            ];
        }

        // Check reviewer
        if ($reviewer === null) {
            return [
                'success' => false,
                'message' => 'Reviewer not found.'
            ];
        }

        // Check conflict of interest
        if (
            strcasecmp(
                trim($article['author']['institution']),
                trim($reviewer['institution'])
            ) === 0
        ) {
            return [
                'success' => false,
                'message' => 'Conflict of interest. Reviewer belongs to the same institution as the author.'
            ];
        }

        $assignments = $this->get_assignments();

        if ($assignments === false) {
            return [
                'success' => false,
                'message' => 'Unable to read assignments.'
            ];
        }

        // Prevent duplicate assignment
        foreach ($assignments as $item) {
            if (
                $item['article_id'] === $article_id &&
                $item['reviewer_id'] === $reviewer_id
            ) {
                return [
                    'success' => false,
                    'message' => 'Reviewer is already assigned to this article.'
                ];
            }
        }

        // Create new assignment
        $new_assignment = [
            'article_id' => $article_id,
            'reviewer_id' => $reviewer_id,
            'assigned_at' => date('Y-m-d H:i:s')
        ];

        $assignments[] = $new_assignment;

        // Save assignment in JSON file
        $json = json_encode($assignments, JSON_PRETTY_PRINT);

        if (file_put_contents($this->assignment_file, $json, LOCK_EX) === false) {
            return [
                'success' => false,
                'message' => 'Unable to save assignment.'
            ];
        }

        return [
            'success' => true,
            'message' => 'Reviewer assigned successfully.',
            'data' => $new_assignment
        ];
    }
}
