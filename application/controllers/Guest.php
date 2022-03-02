<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
// *************************************************************************
// *                                                                       *
// * Softvision India                              *
// * Copyright (c) Softvision India. All Rights Reserved                   *
// *                                                                       *
// *************************************************************************
// *                                                                       *
// * Email: info@softvisionindia.com										*
// * Website: http://softvisionindia.com								   *
// *                                                                       *
// *************************************************************************
// *                                                                       *
// * This software is furnished under a license and may be used and copied *
// * only  in  accordance  with  the  terms  of such  license and with the *
// * inclusion of the above copyright notice.                              *
// *                                                                       *
// *************************************************************************

//LOCATION : application - controller - Guest.php

class Guest extends CI_Controller {

    public function __construct(){
        parent::__construct();
    }


    public function index(){
		
        $data = array();
        $data['page'] = 'Welcome';
        $data['heading'] = 'Access Denied';
        $data['message'] = 'Please contact Administrator';
        $this->load->view('errors/cli/accessdenied', $data);
    }
}