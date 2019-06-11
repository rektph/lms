<?php
defined('BASEPATH') or exit('No direct script access allowed');

class BookApi extends CI_Controller
{

    public function __construct()
    {
        parent::__construct();
        $this->load->library(array("user_agent", "form_validation"));
        if ($this->agent->is_mobile()) {
            if (empty($this->session->post("token"))) show_404();
        } else {
            return;
        }
    }

    public function create()
    {
        if ($this->agent->is_browser()) {
            if ($this->session->userdata("user_type") != 1) show_404();
        }
        
        $json_response = array("response" => 1);
        
        $this->form_validation->set_rules('book_name', 'Book Name', 'trim|required|is_unique[booktbl.book_name]');
        $this->form_validation->set_rules('book_author', 'Book Author', 'trim|required');
        $this->form_validation->set_rules('book_quantity', 'Book Quantity', 'trim|required|numeric');

        if ($this->form_validation->run() == FALSE) {
            $json_response["response"] = 0;
            foreach ($this->form_validation->error_array() as $key => $value) {
                $json_response[$key] = $value;
            }
        } else {
            $data = array(
                "book_name" => $this->input->post("book_name"),
                "book_image" => "",
                "section_id" => $this->input->post("book_section")
            );

            $authors = explode(",", $this->input->post("book_author"));

            $this->load->library('upload');

            if ($this->upload->do_upload('book_image_file')) {
                $data["book_image"] = $this->upload->data('file_name');

                $config2['image_library']   = 'gd2';
                $config2['source_image']    = './images/' . $data["book_image"];
                $config2['create_thumb']    = FALSE;
                $config2['maintain_ratio']  = TRUE;
                $config2['width']           = 200;
                $config2['height']          = 200;

                $this->load->library('image_lib', $config2);

                if (!$this->image_lib->resize()){
                    echo $this->image_lib->display_errors();
                }
                else{
                    $this->image_lib->initialize($config2);
                    $this->image_lib->resize();
                }
            }
            
            if($bookId = $this->Book_model->insertBook($data)) {
                $data = array(
                    "book_id" => $bookId, 
                    "book_code" => "",
                    "status" => 1, 
                    "created_at" => strtotime("now")
                );

                // Populate authorbooktbl
                foreach($authors as $value) {
                    if(!$this->Author_model->setBookAuthor(array("author_id" => $value, "book_id" => $bookId))) {                        
                        $json_response["response"] = 0;
                        $json_response["error"]["author"] = "Unable to insert author_id: " . $value . ".";
                    }
                }

                // Populate itembooktbl
                for($i = 0; $i < $this->input->post("book_quantity"); $i++) {
                    $data["book_code"] = sprintf("%'.03d", $this->Section_model->getCurrentCode(array("section_id" => $this->input->post("book_section")))[0]->section_code_number);
                    if ($this->Book_model->insertBookItem($data)) {
                        if(!$this->Section_model->updateCurrentCode(array("section_code_number" => ($this->Section_model->getCurrentCode(array("section_id" => $this->input->post("book_section")))[0]->section_code_number + 1)), array("section_id" => $this->input->post("book_section")))) {
                            $json_response["response"] = 0;
                            $json_response["error"]["section"] = "Error Updating Section Code Number.";
                        }
                    } else {
                        $json_response["response"] = 0;
                        $json_response["error"]["itembook"] = "Error Inserting Item Book: ". $data["book_code"] . ".";
                    }
                }

            }
        }

        echo json_encode($json_response);
    }

    public function getBooks()
    {
        $json_response = array();
        $json_response["response"] = 1;
        $json_response["book_name"] = $this->Book_model->getBooks("booktbl", array("book_id" => $this->input->post("book_id")))[0]->book_name;
        $json_response["books"] = $this->Book_model->getBookItems($this->input->post("book_id"));
        echo json_encode($json_response);
    }

    public function getSpecificBook()
    {
        if ($this->agent->is_browser()) {
            if ($this->session->userdata("user_type") != 1) show_404();
        }
        $json_response = array();
        $json_response["response"] = 1;
        $json_response["book"] = $this->Book_model->getSpecificBook($this->input->post("itembook_id"))[0];
        switch ($json_response["book"]->status) {
            case 1:
                $json_response["remarks"] = "Available";
                break;
            case 2:
                $json_response["remarks"] = "Reserved by "
                    . $this->User_model->getUsers(array("user_id" => $this->Transaction_model->getTransactionsByBook(array("itembooktbl.itembook_id" => $this->input->post("itembook_id")))[0]->user_id))[0]->username;
                break;
            case 3:
                $json_response["remarks"] = "Borrowed by "
                    . $this->User_model->getUsers(array("user_id" => $this->Transaction_model->getTransactionsByBook(array("itembooktbl.itembook_id" => $this->input->post("itembook_id")))[0]->user_id))[0]->username;
                break;
            case 4:
                $json_response["remarks"] = "Disabled by "
                    . $this->User_model->getUsers(array("user_id" => $this->Transaction_model->getTransactionsByBook(array("itembooktbl.itembook_id" => $this->input->post("itembook_id")))[0]->user_id))[0]->username;
                break;
        }
        echo json_encode($json_response);
    }

    public function updateBook()
    {
        if ($this->agent->is_browser()) {
            if ($this->session->userdata("user_type") != 1) show_404();
        }
        $json_response = array();

        $this->form_validation->set_rules('book-name', 'Book Name', 'trim|required');
        $this->form_validation->set_rules('book-author', 'Book Author', 'trim|required');

        if ($this->form_validation->run() == FALSE) {
            $json_response = array("response" => 0);
            foreach ($this->form_validation->error_array() as $key => $value) {
                $json_response[$key] = $value;
            }
        } else {
            $data = array(
                "book_name" => $this->input->post("book-name"),
                "book_author" => $this->input->post("book-author")
            );
            $where = array(
                "book_id" => $this->input->post("book-id")
            );
            // foreach ($this->input->post() as $key => $value) {
            //     if ($key != "book_id")
            //         $data[$key] = $value;
            //     else
            //         $where[$key] = $value;
            // }
            if ($this->Book_model->updateBook("booktbl", $data, $where)) {
                $json_response["response"] = 1;
            }
        }
        echo json_encode($json_response);
    }

    public function searchBook()
    {
        $json_response = array(
            "books" => $this->Book_model->getBook($this->input->post("book_name"), 0),
            "pages" => $this->Book_model->getBookPages($this->input->post("book_name"))
        );
        echo json_encode($json_response);
    }

    public function pageChange()
    {
        $json_response = array(
            "response" => 1,
            "currentPage" => $this->input->post("page"),
            "pages" => $this->Book_model->getBookPages($this->input->post("book_name")),
            "bookData" => $this->Book_model->getBook($this->input->post("book_name"), ($this->input->post("page") - 1) * 10)
        );

        echo json_encode($json_response);
    }
}
