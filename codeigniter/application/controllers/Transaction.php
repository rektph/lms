<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Transaction extends CI_Controller
{

    // This function is for rendering transaction/pay 
    // and admins only can access this
    public function pay()
    {
        if ($this->session->userdata("user_token") && $this->session->userdata("user_type") == 1) {
            $data = array(
                "page_title" => "Library Management System | Manage Payment",
                "currentActive" => "Manage Payment",
                "pages" => $this->User_model->getUserPages(),
                "users" => $this->User_model->getUserByPages(0)
            );
            $this->load->view("templates/header", $data);
            $this->load->view("components/nav_sidebar");
            $this->load->view("transaction/transaction_pay");
            $this->load->view("transaction/transaction_modal");
            $this->load->view("templates/footer", $data);
        } else {
            show_404();
        }
    }

    // This function is for rendering transaction/reports 
    public function reports()
    {
        if ($this->session->userdata("user_token")) {
            $data = array(
                "page_title" => "Library Management System | Transactions",
                "currentActive" => "Transactions",
                "transactions" => $this->Transaction_model->getTransaction(0),
                "pages" => $this->Transaction_model->getTransactionPages(),
                "transaction_type" => array("Reserve", "Borrow", "Return", "Pay", "Deactivate", "Activate"),
                "transaction_status" => array("primary", "warning", "info", "secondary", "danger", "success")
            );
            if ($this->session->userdata("user_type") == 0) {
                $data["transactions"] = $this->Transaction_model->getTransaction(0, array("transactiontbl.user_id" => $this->session->userdata("user_token")));
                $data["pages"] = $this->Transaction_model->getTransactionPages(array("transactiontbl.user_id" => $this->session->userdata("user_token")));
            }
            $this->load->view("templates/header", $data);
            $this->load->view("components/nav_sidebar");
            $this->load->view("transaction/transaction_reports");
            $this->load->view("templates/footer", $data);
        } else {
            show_404();
        }
    }
}
