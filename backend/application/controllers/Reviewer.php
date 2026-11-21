
<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Reviewer extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();

        $this->load->model('Reviewer_model');

        // Allow Angular frontend to access these APIs
        header('Access-Control-Allow-Origin: http://localhost:4200');
        header('Access-Control-Allow-Methods: GET, POST, OPTIONS');
        header('Access-Control-Allow-Headers: Content-Type');

        // Handle browser preflight request
        if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
            $this->output->set_status_header(204);
            exit;
        }
    }

    // GET: Fetch all articles
    public function articles()
    {
        $articles = $this->Reviewer_model->get_articles();

        if ($articles === false) {
            return $this->send_response([
                'success' => false,
                'message' => 'Unable to load articles.'
            ], 500);
        }

        return $this->send_response([
            'success' => true,
            'data' => $articles
        ]);
    }

    // GET: Fetch all reviewers
    public function reviewers()
    {
        $reviewers = $this->Reviewer_model->get_reviewers();

        if ($reviewers === false) {
            return $this->send_response([
                'success' => false,
                'message' => 'Unable to load reviewers.'
            ], 500);
        }

        return $this->send_response([
            'success' => true,
            'data' => $reviewers
        ]);
    }
    public function assignments()
    {
        $assignments = $this->Reviewer_model->get_all_assignments();

        if ($assignments === false) {
            return $this->send_response([
                'success' => false,
                'message' => 'Unable to load assignments.'
            ], 500);
        }

        return $this->send_response([
            'success' => true,
            'data' => $assignments
        ]);
    }

    // POST: Assign reviewer to article
    public function assign()
    {
        $input = json_decode($this->input->raw_input_stream, true);

        // Check required fields
        if (
            !is_array($input) ||
            !isset($input['article_id'], $input['reviewer_id']) ||
            !is_string($input['article_id']) ||
            !is_string($input['reviewer_id']) ||
            trim($input['article_id']) === '' ||
            trim($input['reviewer_id']) === ''
        ) {
            return $this->send_response([
                'success' => false,
                'message' => 'Article ID and Reviewer ID are required.'
            ], 400);
        }

        $result = $this->Reviewer_model->assign_reviewer(
            trim($input['article_id']),
            trim($input['reviewer_id'])
        );

        $status_code = $result['success'] ? 200 : 400;

        return $this->send_response($result, $status_code);
    }

    // Send JSON response
    private function send_response($data, $status_code = 200)
    {
        return $this->output
            ->set_status_header($status_code)
            ->set_content_type('application/json')
            ->set_output(json_encode($data));
    }
}
