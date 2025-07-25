<?php

if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

class PublicExamtype extends Admin_Controller
{

    public function __construct()
    {
        parent::__construct();
        $this->load->model('publicexamtype_model');
        $this->sch_setting_detail = $this->setting_model->getSetting();
    }

    public function index()
    {
        if (!$this->rbac->hasPrivilege('external_result_examtype', 'can_view')) {
            access_denied();
        }
        $this->session->set_userdata('top_menu', 'Student Information');
        $this->session->set_userdata('sub_menu', 'category/index');
        $data['title']        = $this->lang->line('category_list');
        $category_result      = $this->publicexamtype_model->get();
        $data['categorylist'] = $category_result;
        $this->load->view('layout/header', $data);
        $this->load->view('admin/results/publicresulttype', $data);
        $this->load->view('layout/footer', $data);
    }

    public function view($id)
    {
        if (!$this->rbac->hasPrivilege('external_result_examtype', 'can_view')) {
            access_denied();
        }
        $data['title']    = $this->lang->line('category_list');
        $category         = $this->publicexamtype_model->get($id);
        $data['category'] = $category;
        $this->load->view('layout/header', $data);
        $this->load->view('admin/results/resulttypeshow', $data);
        $this->load->view('layout/footer', $data);
    }

    public function delete($id)
    {
        if (!$this->rbac->hasPrivilege('external_result_examtype', 'can_delete')) {
            access_denied();
        }
        $data['title'] = $this->lang->line('category_list');
        $this->publicexamtype_model->remove($id);
        $this->session->set_flashdata('msgdelete', '<div class="alert alert-success text-left">' . $this->lang->line('delete_message') . '</div>');
        redirect('admin/results/publicexamtype/index');
    }

    public function create()
    {
        if (!$this->rbac->hasPrivilege('external_result_examtype', 'can_add')) {
            access_denied();
        }
        $data['title']        = $this->lang->line('add_category');
        $category_result      = $this->publicexamtype_model->get();
        $data['categorylist'] = $category_result;
        $this->form_validation->set_rules('category', $this->lang->line('category'), 'trim|required|xss_clean');
        if ($this->form_validation->run() == false) {
            $this->load->view('layout/header', $data);
            $this->load->view('admin/results/publicresulttype', $data);
            $this->load->view('layout/footer', $data);
        } else {
            $data = array(
                'examtype' => $this->input->post('category'),
                'session_id' =>$this->setting_model->getCurrentSession(),
            );
            $this->publicexamtype_model->add($data);
            $this->session->set_flashdata('msg', '<div class="alert alert-success text-left">' . $this->lang->line('success_message') . '</div>');
            redirect('admin/results/publicexamtype/index');
        }
    }

    public function edit($id)
    {
        if (!$this->rbac->hasPrivilege('external_result_examtype', 'can_edit')) {
            access_denied();
        }
        $data['title']        = $this->lang->line('edit_category');
        $category_result      = $this->publicexamtype_model->get();
        $data['categorylist'] = $category_result;
        $data['id']           = $id;
        $category             = $this->publicexamtype_model->get($id);
        $data['category']     = $category;
        $this->form_validation->set_rules('category', $this->lang->line('category'), 'trim|required|xss_clean');
        if ($this->form_validation->run() == false) {
            $this->load->view('layout/header', $data);
            $this->load->view('admin/results/publicresulttypeedit', $data);
            $this->load->view('layout/footer', $data);
        } else {
            $data = array(
                'id'       => $id,
                'examtype' => $this->input->post('category'),
            );
            $this->publicexamtype_model->add($data);
            $this->session->set_flashdata('msg', '<div class="alert alert-success text-left">' . $this->lang->line('update_message') . '</div>');
            redirect('admin/results/publicexamtype/index');
        }
    }

}
