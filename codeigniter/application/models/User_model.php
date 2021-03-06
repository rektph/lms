<?php
defined('BASEPATH') or exit('No direct script access allowed');

class User_model extends CI_Model
{

    public function __construct()
    {
        parent::__construct();
    }

    public function getUsers($where = NULL)
    {
        if ($where !== NULL) {
            $this->db->where($where);
        }
        $query = $this->db->select("user_id, username, status, user_type")->get("usertbl");
        return $query->num_rows() > 0 ? $query->result() : false;
    }

    public function searchUser($search)
    {
        $this->db->like("username", $search, "both");
        $this->db->limit("10");
        $query = $this->db->select("user_id, username, status, user_type")->get("usertbl");
        return $query->num_rows() > 0 ? $query->result() : false;
    }

    public function createUser($data)
    {
        $this->db->insert("usertbl", $data);
        return $this->db->affected_rows();
    }

    public function updateUser($ndata, $id = NULL)
    {
        if (!empty($id)) {
            $this->db->where($id);
        }

        $this->db->update("usertbl", $ndata);
        return $this->db->affected_rows();
    }

    public function hasValidCredentials($username, $password)
    {
        $this->db
            ->select("user_id, username, status, user_type")
            ->from('usertbl')
            ->where(array(
                'username' => $username,
                'password' => sha1($password)
            ));
        $query = $this->db->get();
        return $query->num_rows() > 0 ? $query->result() : false;
    }

    public function getUserByPages($start, $searchText = "")
    {
        $query = $this->db->select("user_id, username, user_type, status")
            ->from("usertbl")
            ->limit(10, $start)
            ->like("username", $searchText, "both")
            ->get();
        return $query->num_rows() > 0 ? $query->result() : false;
    }

    public function getUserPages($searchText = "")
    {
        $query = $this->db->select("user_id, username, user_type, status")
            ->from("usertbl")
            ->like("username", $searchText, "both")
            ->get();
        return ceil($query->num_rows() / 10);
    }
}
