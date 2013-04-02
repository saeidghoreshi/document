<?php

class Finance extends Controller
{
	
	/**
	* @var beanstream
	*/
	public $beanstream;
	/**
	* @var finance_model
	*/
	public $finance_model;
	/**
	* @var permissions_model
	*/
	public $permissions_model;
	public $org_model;
	public $entity_model;
	/**
	* @var endeavor_model
	*/
	public $endeavor_model;
	
	public function __construct()
	{
		parent::Controller();
		$this->load->model('finance_model');
		$this->load->model('endeavor_model');
		$this->load->model('permissions_model');
		$this->load->model('prize_model');
		$this->load->model('entity_model');
		$this->load->model('org_model');
		$this->load->library('page');
        $this->load->library('input');
		$this->load->library('result');
	}
	private function load_window()
    {
        $this->load->library('window');//$this->window->set_js_path("/assets/js2/finance/");
        $this->window->set_css_path("/assets/css/");
        $this->window->set_js_path("/assets/js/");
    }
	
	
	/**CRON METHODS CANNOT ACCESS ACTIVE USER OR ACTIVE ORG FUNCTIONS*/
	public function cron_check_pending_transactions()
	{
		$this->finance_model->check_pending_transactions();
	}                                                          
	public function cron_batchebp()
	{
		$this->finance_model->batchebp();
	}  
    
    //Signing AUTHORITY
    public function window_manage_authority($id=false)
    {                                                                                                            
        $this->load_window();                                 
        $this->window->add_css('');
                                                
        $this->window->add_js('components/authority/grids/spectrumgrids.bankaccount.js');
        $this->window->add_js('components/authority/grids/spectrumgrids.withdraw.js');
        $this->window->add_js('components/authority/grids/spectrumgrids.sa.assignment.js');
        $this->window->add_js('components/authority/grids/spectrumgrids.rule.js');
        $this->window->add_js('components/authority/grids/spectrumgrids.motion.js');
                                                                     
                                                                     
                                                                     
        
        $this->window->add_js('components/users/grids/user.js');
        $this->window->add_js('components/users/grids/org_roles.js');
        
        $this->window->add_js('components/authority/forms/signing_authority.js');
        $this->window->add_js('components/authority/windows/signing_authority.js');
        $this->window->add_js('components/authority/forms/bankaccount.js');
        $this->window->add_js('components/authority/forms/forms.js');
        $this->window->add_js('components/authority/windows/spectrumwindow.authority.js');
        $this->window->add_js('components/authority/controller.js'); 
        $this->window->add_js('components/authority/toolbar.js');
        
        
        $this->window->set_header('Manage Singning Authorities');
        $this->window->set_body($this->load->view('finance/finance.main_signing_authority.php',null,true));
        $this->window->json();
    }
    public function post_bank_skipmotion()
    {
        $name           = $this->input->get_post('bankaccount_name');
        $transit        = $this->input->get_post('transit');
        $institution    = $this->input->get_post('institution');
        $account        = $this->input->get_post('account');
        $description    = $this->input->get_post('description');
        $bankname       = $this->input->get_post('bankname');
        
        $creator=   $this->permissions_model->get_active_user();
        $owner  =   $this->permissions_model->get_active_org();
        $entity_id= $this->org_model->get_entity_id_from_org($owner);//entity id of org
        $enabled='t';//of course its enabled, its get started screen
        
        $bankaccount_id = $this->input->get_post('bankaccount_id');
        
        if($bankaccount_id != -1)
        {
            $result=(int)$this->finance_model->update_bankaccount($bankaccount_id,$name,$transit,$institution,$account,$enabled,$bankname,$creator);
        }
        else
        {
            $result=(int)$this->finance_model->new_bankaccount($name,$transit,$institution,$account,$bankname,$entity_id,$enabled,$creator,$owner);
        }
        
        //($name,$transit,$institution,$account,$bankname,$description);
   
        $this->result->success($result);         
    }
    public function json_get_motions()
    {
        $status_type_name=$this->input->get_post("status_type_name");
        
        $result=$this->finance_model->get_motions($status_type_name);
        foreach($result as $i=>$v)
            $result[$i]["created_on_display"]=($result[$i]["created_on"]!=null)?date('Y/m/d',strtotime($v['created_on'])):null;
        $this->result->json_pag($result);
    }
    public function json_get_bankaccounts()
    {
        $result=$this->finance_model->get_bankaccounts();
    	$this->result->json_pag($result);
    }                             
    public function json_get_withdraws()
    {
        $result=$this->finance_model->get_withdraws();
        $this->result->json_pag($result);
    }
    public function json_get_rules()
    {
        $result=$this->finance_model->get_rules();
        $this->result->json_pag($result);
    }
    public function json_get_rules_2()
    {
        $result=$this->finance_model->get_rules_type();
        $this->result->json_pag($result);
    }                           
    public function json_new_motion_vote()
    {
        $motion_id      =$this->input->get_post('motion_id');
        $vote           =$this->input->get_post('vote');
        
        $result = $this->finance_model->new_motion_vote($motion_id,$vote);
        
        $user = $this->permissions_model->get_active_org();
        $org  = $this->permissions_model->get_active_user();
        
        //if this is not a withdraw motion, this function will do nothing so its ok
        $this->finance_model->handle_approved_withdraw_motion($motion_id,$user,$org);
        
        $this->result->success(  $result);                    
    }
    public function json_new_motion_bankaccount_add()
    {
        $name           =$this->input->get_post('bankaccount_name');//fixed this, now its same as database and form
        $transit        =$this->input->get_post('transit');
        $institution    =$this->input->get_post('institution');
        $account        =$this->input->get_post('account');
        $description    =$this->input->get_post('description');
        $bankname       =$this->input->get_post('bankname');
        
        $result=$this->finance_model->new_motion_bankaccount_add($name,$transit,$institution,$account,$bankname,$description);
        $this->result->success( $result[0]["new_motion_bankaccount"]);            
    }
    public function json_new_motion_bankaccount_del()
    {
        $bankaccont_id  =$this->input->get_post('bankaccount_id');
        
        $result=$this->finance_model->new_motion_bankaccount_del($bankaccont_id);
        $this->result->success(  $result[0]["new_motion_bankaccount"]);
    }
    public function json_new_motion_assignment_cancel()
    {
        $assignment_id  =$this->input->get_post('assignment_id');
        $description    =$this->input->get_post('description');
        
        $result=$this->finance_model->new_motion_assignment_cancel($assignment_id,$description);
        $this->result->success($result[0]["new_motion_assignment"]);            
    }
    public function json_new_motion_assignment_add()
    {
        $user_id        =$this->input->get_post('user_id');
        $role_id        =$this->input->get_post('role_id');
        
        $result=$this->finance_model->new_motion_assignment_add($user_id,$role_id);
        $this->result->success(  $result[0]["new_motion_assignment"]);            
    }
    public function json_new_motion_assignment_del()
    { 
    	$assignment_id  =(int)$this->input->get_post('assignment_id');
        $description    =rawurldecode($this->input->get_post('description'));//undo the javascript 'escape' functoin
        
        $result=$this->finance_model->new_motion_assignment_del($assignment_id,$description);
        $this->result->success(  $result[0]["new_motion_assignment"]);            
    }
    public function json_new_motion_withdraw()
    {
        $bankaccount_id =$this->input->get_post('bankaccount_id');
        $amount         =$this->input->get_post('amount');
        $fees           =$this->input->get_post('fees');
        $total          =$this->input->get_post('total');
        $entity_id      =$this->input->get_post('entity_id');
        $description    =$this->input->get_post('description');
        
        $motion_id = $this->finance_model->new_motion_withdraw($bankaccount_id,$amount,$fees,$total,$entity_id,$description);
      
        $user = $this->permissions_model->get_active_org();
        $org  = $this->permissions_model->get_active_user();
        //check if this withdraw motion is already approved, handle it with transactions and everything
        $this->finance_model->handle_approved_withdraw_motion($motion_id,$user,$org);
        
        $this->result->success( 1 );            
    }
    public function json_new_motion_rule()
    {
        $rule_value     =$this->input->get_post('rule_value');
        $rule_type_id   =$this->input->get_post('rule_type_id');
        $description    =$this->input->get_post('description');
        
        $result=$this->finance_model->new_motion_rule($rule_value,$rule_type_id,$description);
        $this->result->success( $result[0]["new_motion_rule"]);            
    }
    public function json_get_sa_assignments()
    {
        $result=$this->finance_model->get_sa_assignments();
        $this->result->json_pag($result);
    }
    public function json_reset_sa()
    {
    	
       $result=$this->finance_model->reset_sa();
       $this->result->success(  $result[0]["reset_sa"]);
    }
    public function json_getOrgAddresses()
    {
        $address_type_id=$this->input->get_post("address_type_id");    
        $result         =$this->finance_model->getOrgAddresses($address_type_id);
        $this->result->json_pag_store($result);
    }
    public function json_getOrgEmails()
    {                                   
        $result         =$this->finance_model->getOrgEmails();
        $this->result->json_pag_store($result);    
    }    
    public function json_get_eftfees()
    {
        $result         =$this->finance_model->get_eftfees();
        $this->result->success( $result[0]);            
    }   
    
    
    public function json_get_motion_status()
    {
        $result         =$this->finance_model->get_motion_status();
        $result[]       =array("type_id"=>-1,"type_name"=>"All");
        $this->result->json_pag_store($result);                    
    }
    public function json_get_eft_status()
    {
        $result         =$this->finance_model->get_eft_status();
        $result[]       =array("type_id"=>-1,"type_name"=>"All");
        $this->result->json_pag_store($result);                    
    }
                        
    //Finance and Accounting
    
    //Transaction History
    //very general  Not in use but keeping it
    public function window_manage_finance($id=false) 
    {                                                                                                            
        $this->load_window();                                 
        $this->window->add_css('');
        
        //$this->window->add_js('classes/financeToolbar.js');
        $this->window->add_js('components/finance/grids/spectrumgrids.transaction.js');
        $this->window->add_js('components/finance/grids/spectrumgrids.invoice.js');
        //$this->window->add_js('components/finance/grids/spectrumgrids.draftinvoice.js');
        $this->window->add_js('components/finance/grids/spectrumgrids.invoiceItem.js');
        $this->window->add_js('components/finance/grids/spectrumgrids.chargeItem.js');
        $this->window->add_js('components/finance/grids/spectrumgrids.paymentList.js');
        $this->window->add_js('components/finance/grids/spectrumgrids.entity.js');
        //$this->window->add_js('components/finance/grids/spectrumgrids.orgRecursive.js');
        $this->window->add_js('components/finance/controllerPaymentTestCase.js');
        
        $this->window->add_js('components/finance/controller.js');
        $this->window->add_js('components/finance/windows/spectrumwindow.finance.js');
        $this->window->add_js('components/finance/forms/forms.js');
        $this->window->add_js('components/finance/toolbar.js');
        
        $this->window->set_header('Manage Finances');
        $this->window->set_body($this->load->view('finance/finance.main.php',null,true));
        $this->window->json();
    }
    
    public function window_manage_invoices() 
    {   
        $this->load_window();                                 
        $this->window->add_css('');
        
        $this->window->add_js('components/finance/widgets/payment.js');
        
        $this->window->add_js('components/finance/grids/spectrumgrids.invoice.js');
        $this->window->add_js('components/finance/grids/spectrumgrids.invoiceItem.js');
        $this->window->add_js('components/finance/grids/spectrumgrids.chargeItem.js');
        $this->window->add_js('components/finance/grids/spectrumgrids.entity.js');
        
        $this->window->add_js('components/finance/controller.js');
        $this->window->add_js('components/finance/windows/spectrumwindow.finance.js');
        $this->window->add_js('components/finance/forms/forms.js');
        $this->window->add_js('components/finance/invoice-toolbar.js');
        
        $this->window->set_header('Manage Invoices');
        $this->window->set_body($this->load->view('finance/finance.main.invoice.php',null,true));
        $this->window->json();
    }
    public function window_manage_reports()
    {                                                                                                            
        $this->load_window();                                 
        $this->window->add_css('');
        
        $this->window->add_js('components/finance/grids/spectrumgrids.transaction.js');
        
        $this->window->add_js('components/finance/controller.js');
        $this->window->add_js('components/finance/windows/spectrumwindow.finance.js');
        $this->window->add_js('components/finance/forms/forms.js');
        $this->window->add_js('components/finance/report-toolbar.js');
        
        $this->window->set_header('Manage Reports');
        $this->window->set_body($this->load->view('finance/finance.main.report.php',null,true));
        $this->window->json();
    }
    public function window_manage_transaction_payment($id=false) 
    {                                                                                                            
        $this->load_window();                                 
        $this->window->add_css('');
        
        $this->window->add_js('components/finance/grids/spectrumgrids.transaction.js');
        $this->window->add_js('components/finance/grids/spectrumgrids.paymentList.js');
        $this->window->add_js('components/finance/grids/spectrumgrids.entity.js');     
        
        $this->window->add_js('components/finance/controller.js');
        $this->window->add_js('components/finance/windows/spectrumwindow.finance.js');
        $this->window->add_js('components/finance/forms/forms.js');
        $this->window->add_js('components/finance/transaction-toolbar.js');
        
        $this->window->set_header('Manage Transactions & Payments');
        $this->window->set_body($this->load->view('finance/finance.main.transaction.php',null,true));
        $this->window->json();
    }
    public function window_manage_setup($id=false) 
    {                                                                                                            
        $this->load_window();                                 
        $this->window->add_css('');
        
        $this->window->add_js('components/finance/grids/spectrumgrids.chargeItem.js');
        $this->window->add_js('components/finance/grids/spectrumgrids.entity.js');
        $this->window->add_js('components/finance/controllerPaymentTestCase.js');
        
        $this->window->add_js('components/finance/controller.js');
        $this->window->add_js('components/finance/windows/spectrumwindow.finance.js');
        $this->window->add_js('components/finance/forms/forms.js');
        $this->window->add_js('components/finance/setup-toolbar.js');
        
        $this->window->set_header('Manage Settings');
        $this->window->set_body($this->load->view('finance/finance.main.setup.php',null,true));
        $this->window->json();
    }
    public function window_online_report($id=false) 
    {                                                                                                            
        $this->load_window();                                 
        $this->window->add_css('');
        
        $this->window->add_js('components/finance/controller.js');
        $this->window->add_js('components/finance/windows/spectrumwindow.finance.js');
        $this->window->add_js('components/finance/forms/forms.js');
        $this->window->add_js('components/finance/onlinereport-toolbar.js');
        
        $this->window->set_header('Reports');
        $this->window->set_body($this->load->view('finance/finance.main.onlinereport.php',null,true));
        $this->window->json();    
    }
    
    //Transaction History END
    public function json_get_transaction_history()
    {
        $currency_type_id   =$this->input->get_post("currency_type_id");
        $account_type_id    =$this->input->get_post("account_type_id");
        $txn_num            =$this->input->get_post("txn_num");
        $action_id          =$this->input->get_post("action_id");
        
        $fromDate           =$this->input->get_post("fromDate");
        $toDate             =$this->input->get_post("toDate");
        
        $result=$this->finance_model->get_transaction_history($currency_type_id,$account_type_id,$txn_num,$action_id,$fromDate,$toDate);
        /*foreach($result as $i=>$v)                                                                                                                    
            $result[$i]["trans_datetime_display"]   =($result[$i]["trans_datetime"]!=null)?date('Y/m/d (H:i:s)',strtotime($v['trans_datetime'])):null;
        */    
        $this->result->json_pag($result);    
        
    }                                                 
    public function json_get_invoices()
    {
        $fromDate       =$this->input->get_post("fromDate");
        $toDate         =$this->input->get_post("toDate");
        $dateTypeList   =$this->input->get_post("dateTypeList");
                                        
        $invoice_type_id=(int)$this->input->get_post('invoice_type_id');
        if(!$invoice_type_id)           
            $invoice_type_id='0';
            
        $result=$this->finance_model->get_invoices($invoice_type_id,$fromDate,$toDate,$dateTypeList);
        //cause huge delay
        /*
        foreach($result as $i=>$v)
        {                 
            $result[$i]["date_issued_display"]      =($result[$i]["date_issued"]!=null)?    date('Y-m-d',strtotime($v['date_issued']))      :null;
            $result[$i]["date_due_display"]         =($result[$i]["date_due"]!=null)?       date('Y-m-d',strtotime($v['date_due']))         :null;
            $result[$i]["created_on_display"]       =($result[$i]["created_on"]!=null)?     date('Y-m-d (H:i)',strtotime($v['created_on'])) :null;
        }
        */
            
        $this->result->json_pag($result);    
    }
    public function json_get_invoice_types()
    {
        $result=array(
                array("type_name"=>"All"        ,"type_id"=>0),
                array("type_name"=>"Payable"    ,"type_id"=>1),
                array("type_name"=>"Receiveable","type_id"=>2),
                array("type_name"=>"Paid"       ,"type_id"=>3),
                array("type_name"=>"Cancelled"  ,"type_id"=>4),
                array("type_name"=>"OverDue"    ,"type_id"=>5)
                );
        $this->result->json_pag_store($result);                
    }
    public function json_get_invoice_items()
    {
        $invoice_id       =$this->input->get_post("invoice_id");                    
              
        $result=$this->finance_model->get_invoice_items($invoice_id);
        $this->result->json_pag($result);        
    }
    public function json_getHierarchicalEntities()
    {
        $org_type_id        =$this->input->get_post("org_type_id");
        $target_org_type_id =$this->input->get_post("target_org_type_id");
        
        $result=$this->finance_model->getHierarchicalEntities($org_type_id,$target_org_type_id);
        $this->result->json_pag($result);
    }       
    public function json_get_income_statement()
    {
        $currency_type_id=$this->input->get_post("currency_type_id");
        
        $result=$this->finance_model->get_income_statement($currency_type_id);
        foreach($result as $i=>$v)
            $result[$i]["trans_datetime_display"]       =($result[$i]["trans_datetime"]!=null)?date('Y/m/d (H:i:s)',strtotime($v['trans_datetime'])):null;
        $this->result->json_pag($result);    
    }
    public function json_get_balancesheet()
    {
        $currency_type_id=$this->input->get_post("currency_type_id");
        
        $result=$this->finance_model->get_balancesheet($currency_type_id);
        foreach($result as $i=>$v)
            $result[$i]["trans_datetime_display"]       =($result[$i]["trans_datetime"]!=null)?date('Y/m/d (H:i:s)',strtotime($v['trans_datetime'])):null;
        $this->result->json_pag($result);    
    }                  
    public function json_get_pending_invoices()
    {                                
        $result=$this->finance_model->get_pending_invoices();
        foreach($result as $i=>$v)
        {
            $result[$i]["date_issued_display"]      =($result[$i]["date_issued"]!=null)?date('Y/m/d',strtotime($v['date_issued'])):null;
            $result[$i]["date_due_display"]         =($result[$i]["date_due"]!=null)?date('Y/m/d',strtotime($v['date_due'])):null;
            $result[$i]["created_on_display"]       =($result[$i]["created_on"]!=null)?date('Y/m/d',strtotime($v['created_on'])):null;
        }
            
        $this->result->json_pag($result);    
    }
    public function json_get_currencytypes()
    {                              
        $result =$this->finance_model->get_currencytypes();
        $this->result->json_pag_store($result);        
    }
    public function json_get_currencyowntypes()
    {                                                   
        $result=$this->finance_model->get_currencyowntypes();
        $this->result->json_pag_store($result);        
    }
    public function json_get_account_types()
    {                                                        
        $result     =$this->finance_model->get_account_types();
        $this->result->json_pag_store($result);        
    }
    public function json_get_charge_types()
    {
        $result=$this->finance_model->get_charge_types();
        $this->result->json_pag_store($result);            
    }        
    public function json_get_child_entities()
    {
        $parentEntityId =$this->input->get_post("parentEntityId");
        
        //Filtering Parameters
        $country_id =$this->input->get_post("country_id");
        $region_id  =$this->input->get_post("region_id");
        
        $result=$this->finance_model->get_child_entities($parentEntityId,$country_id,$region_id);
        $this->result->json_pag($result);                
    }
    public function json_get_card_types()
    {
        $result=array(
                array("type_name"=>"MASTER CARD"    ,"type_id"=>'4'),
                array("type_name"=>"VISA CARD"      ,"type_id"=>'3'),
                array("type_name"=>"INTERAC"        ,"type_id"=>'2'),
                array("type_name"=>"INTERNAL"       ,"type_id"=>'1')
                );
        $this->result->json_pag_store($result);                
    }
    public function json_get_receivers()
    {
        $result=
        array
        (
            array("entity_name"=>"NSA Canada"       ,"entity_id"=>'5')
        );
        $this->result->json_pag_store($result);                
    }
    public function json_get_masters()
    {
        $result=
        array
        (
            array("entity_name"=>"NSA Canada"       ,"entity_id"=>'5')
        );
        $this->result->json_pag_store($result);                
    }
    public function json_get_slaves()
    {
        $result=
        array
        (
            array("entity_name"=>"revelstoke"       ,"entity_id"=>'80'),
            array("entity_name"=>"Vernon Coed Slopitch League"  ,"entity_id"=>'6')
        );
        $this->result->json_pag_store($result);                
    }
    public function json_get_user_addresses()
    {                                 
        $result=$this->finance_model->get_user_addresses();
        $this->result->json_pag_store($result);
    }
    public function json_get_payment_history()
    {                                                             
        $result=$this->finance_model->get_payment_history();
        //cause huge delay
        /*foreach($result as $i=>$v)                                                                                                                     
            $result[$i]["created_on_display"]                =($result[$i]["created_on"]!=null)?date('Y/m/d (H:i:s)',strtotime($v['created_on'])):null;
        */    
        $this->result->json_pag($result);    
    }
    public function json_new_charge_item()
    {
        $charge_code            =$this->input->get_post("charge_code");
        $charge_descr           =$this->input->get_post("charge_descr");
              
        $result=$this->finance_model->new_charge_item($charge_code,$charge_descr);
        $this->result->success(  $result[0]["new_charge_item"]);
    }
    public function json_add_invoice_item()
    {
        $invoice_id                 =$this->input->get_post("invoice_id");
        $charge_type_id             =$this->input->get_post("charge_type_id");
        $charge_price               =$this->input->get_post("charge_price");
        $charge_cost                =$this->input->get_post("charge_cost");
        $quantity                   =$this->input->get_post("quantity");
        $invoice_item_description   =$this->input->get_post("invoice_item_description");
        $tax_applies                =$this->input->get_post("tax_applies");
                                                                                                             
        $result=$this->finance_model->add_invoice_item($invoice_id,$charge_type_id,$invoice_item_description,$charge_price,$charge_cost,$quantity,$tax_applies);
        $this->result->success(  $result[0]["add_invoice_item"]);    
    }
    public function json_delete_invoice_item()
    {
        $invoice_item_id            =$this->input->get_post("invoice_item_id");                                   
        $tax_applies                =$this->input->get_post("tax_applies");
        
        $result=$this->finance_model->delete_invoice_item($invoice_item_id,$tax_applies);
        $this->result->success(  $result[0]["delete_invoice_item"]);        
    }
    public function json_apply_tax_invoice()
    {
        $invoice_id     =$this->input->get_post("invoice_id");
        $tax_applies    =$this->input->get_post("tax_applies");
        
        $result=$this->finance_model->apply_tax_invoice($invoice_id,$tax_applies);
        $this->result->success( $result[0]["apply_tax_invoice"]);
    }
    public function json_close_invoice()
    {
        $invoice_id             =$this->input->get_post("invoice_id");
        $recipient_eids         =$this->input->get_post("recipient_eids");
        $recipient_custom_nums  =$this->input->get_post("recipient_custom_nums"); 
        $description            =$this->input->get_post("description");
        $due_date               =$this->input->get_post("due_date");
        $just_save              =$this->input->get_post("just_save");
        $keep_draft_cb          =$this->input->get_post("keep_draft_cb");
        $tax_applies            =$this->input->get_post("tax_applies");
        
        $result=$this->finance_model->close_invoice($invoice_id,$recipient_eids,$recipient_custom_nums ,$description,$due_date,$just_save,$keep_draft_cb,$tax_applies);
        $this->result->success(  $result[0]["close_invoices"]);    
    }
    public function json_delete_invoice()
    {
        $invoice_id     =$this->input->get_post("invoice_id");                                               
        $result=$this->finance_model->delete_invoice($invoice_id);
        $this->result->success(  $result[0]["delete_invoice"]);    
    }
    public function json_get_applied_tax_status_invoice()
    {
        $invoice_id     =$this->input->get_post("invoice_id");
        $result=$this->finance_model->get_applied_tax_status_invoice($invoice_id);
        $this->result->success(  $result[0]["get_applied_tax_status_invoice"]);    
    }
    public function json_change_invoice_status()
    {
        $invoice_id         =$this->input->get_post("invoice_id");
        $invoice_status_id  =$this->input->get_post("invoice_status_id");                        
                                                                                                             
        $result=$this->finance_model->change_invoice_status($invoice_id,$invoice_status_id);
        $this->result->success(  $result[0]["change_invoice_status"]);            
    }
    public function json_reset_transactions()
    {
        $result=$this->finance_model->reset_transactions();
        $this->result->success(  $result[0]["reset_transactions"]);            
    }
    public function json_setup_entities_payment_plan()
    {
        $master_entity_id       =$this->input->get_post("master_entity_id");
        $slave_entity_id        =$this->input->get_post("slave_entity_id");
        $charge_type_id         =$this->input->get_post("charge_type_id");
        $amount                 =$this->input->get_post("amount");
        $currency_type_id       =$this->input->get_post("currency_type_id");
        
        $result=$this->finance_model->setup_entities_payment_plan($master_entity_id,$slave_entity_id,$charge_type_id,$amount,$currency_type_id);
        $this->result->success(  $result[0]["setup_entities_payment_plan"]);
    }
    public function json_get_unpaid_deposits()
    {
        $result=$this->finance_model->get_unpaid_deposits();
        $this->result->json_pag($result);    
    }
    public function json_delete_charge_type()
    {
        $charge_type_id  =$this->input->get_post("charge_type_id");
        
        $result=$this->finance_model->delete_charge_type($charge_type_id);
        $this->result->success( $result[0]["delete_charge_type"]);    
    }
    public function json_update_invoice_item()
    {
        $invoice_item_id    =$this->input->get_post("invoice_item_id");
        $charge_cost        =$this->input->get_post("charge_cost");
        $charge_price       =$this->input->get_post("charge_price");
        $quantity           =$this->input->get_post("quantity");
        
        $result=$this->finance_model->update_invoice_item($invoice_item_id,$charge_price,$charge_cost,$quantity);
        $this->result->success( $result[0]["update_invoice_item"]);
    }
    public function json_add_wallet_money()
    {
        $a_e_id             = $this->permissions_model->get_active_entity();
        $currency_type_id   =$this->input->get_post("currency_type_id");
        $amount             =$this->input->get_post("amount");
        
        $result=$this->finance_model->add_wallet_money($a_e_id,$amount,$currency_type_id);
        $this->result->success(   1); 
    } 
    public function json_add_wallet_moneys()
    {
        $entity_amount_s    =$this->input->get_post("entity_amount_s");
        $currency_type_id   =$this->input->get_post("currency_type_id");
        
        $result=$this->finance_model->add_wallet_moneys($entity_amount_s,$currency_type_id);
       $this->result->success(1); 
    } 
    public function json_release_deposit_invoice()
    {      
        $invoice_id         =$this->input->get_post("invoice_id");
        $amount             =$this->input->get_post("amount");
        
        $result=$this->finance_model->release_deposit_invoice($invoice_id,$amount);
        $this->result->success( $result[0]["release_deposit_invoice"]);    
    }
    public function json_get_deposit_balance()//total unused deposit left
    {
        $master_entity_id   =$this->input->get_post("master_entity_id");
        $slave_entity_id    =$this->input->get_post("slave_entity_id");
        $currency_type_id   =$this->input->get_post("currency_type_id");
        
        
        $result=$this->finance_model->get_deposit_balance($master_entity_id ,$slave_entity_id , $currency_type_id );
        $this->result->success($result[0]["get_deposit_balance"]);    
    }
    public function json_get_my_transaction_types()
    {
        $a_e_id             = $this->permissions_model->get_active_entity();
        
        $result=$this->finance_model->get_my_transaction_types($a_e_id);
        $this->result->json_pag($result);
    }
    public function json_get_slaveTypeStore()
    {
        $org_type_id = $this->permissions_model->get_active_org_type();
        
        //SSI Level
        if($org_type_id==1)//==> Me/A/L/T
            $result=array(array("type_id"=>"1","type_name"=>"ME"),array("type_id"=>"2","type_name"=>"Association"));
        //Association Level
        if($org_type_id==2)//==> Me/L/T
            $result=array(array("type_id"=>"2","type_name"=>"ME"),array("type_id"=>"3","type_name"=>"League"));
        //League Level
        if($org_type_id==3)//==> Me/T
            $result=array(array("type_id"=>"3","type_name"=>"ME"),array("type_id"=>"6","type_name"=>"Team"));
        //Team Level
        if($org_type_id==6)//==> Me/
            $result=array(array("type_id"=>"6","type_name"=>"ME"));
            
       $this->result->json_pag_store($result);
    }
    public function json_org_levels_store()
    {
        $org_type_id = $this->permissions_model->get_active_org_type();
        
        //SSI Level
        if($org_type_id==1)//==> itself/A/L/T/T
            $result=array(array("org_type_id"=>"1","org_type_name"=>"SSI"),array("org_type_id"=>"2","org_type_name"=>"Associations"),array("org_type_id"=>"3","org_type_name"=>"Leagues"),array("org_type_id"=>"6","org_type_name"=>"Teams"),array("org_type_id"=>"4","org_type_name"=>"Tournaments"));
        //Association Level
        if($org_type_id==2)//==> itself/L/T/T
            $result=array(array("org_type_id"=>"2","org_type_name"=>"Associations"),array("org_type_id"=>"3","org_type_name"=>"Leagues"),array("org_type_id"=>"6","org_type_name"=>"Teams"),array("org_type_id"=>"4","org_type_name"=>"Tournaments"));
        //League Level
        if($org_type_id==3)//==> itself/T/T
            $result=array(array("org_type_id"=>"3","org_type_name"=>"Leagues"),array("org_type_id"=>"6","org_type_name"=>"Teams"),array("org_type_id"=>"4","org_type_name"=>"Tournaments"));
        //Team Level
        if($org_type_id==6)//==> itself
            $result=array(array("org_type_id"=>"6","org_type_name"=>"Teams"));
            
       $this->result->json_pag_store($result);    
    }
    public function json_final_save_invoice()
    {
        $invoice_id         =$this->input->get_post("invoice_id");
        $issuerinfo_address =$this->input->get_post("issuerinfo_address");
        $issuerinfo_email   =$this->input->get_post("issuerinfo_email");
        $invoice_comment    =$this->input->get_post("invoice_comment");
        
        $result=$this->finance_model->final_save_invoice($invoice_id,$issuerinfo_address,$issuerinfo_email,$invoice_comment);
        $this->result->success($result[0]["final_save_invoice"]);        
    }
    public function json_invoice_date_type_list()
    {
        $invoiceType    =$this->input->get_post("invoiceType");
        if($invoiceType=='invoice')
        {
            $result=array
            (
                array("type_id"=>1,"type_name"=>"Issue Date"),
                array("type_id"=>2,"type_name"=>"Due Date"),
                array("type_id"=>3,"type_name"=>"Created Date")
            );  
        }
        if($invoiceType=='draft')
        {
            $result=array
            (
                array("type_id"=>3,"type_name"=>"Created Date")
            );  
        }
        $this->result->json_pag_store($result);
    }
    public function json_getEntityTaxes()
    {
        $result =$this->finance_model->getEntityTaxes();
        //echo json_encode(array("success"=>true,"result"=>$result)); 
        $this->result->success($result);          
    }                                                
    public function json_getReportsContent()
    {                    
        $report_type   =$this->input->get_post("report_type");
        $fromdate       =trim($this->input->get_post("onlinereport_fromdate"));
        $todate         =trim($this->input->get_post("onlinereport_todate"));
        
        if($report_type==1)
        {
            //PAYMENTS DONE
            $payment_type_name="external";
            $paymentHistory=$this->finance_model->get_paymentHistory($fromdate,$todate,$payment_type_name);
            $data=$this->load->view('finance/reports/onlinereport.payments.php',array("payments"=>$paymentHistory),true);
        }
        if($report_type==2)
        {
            //PAYMENTS DONE
            $payment_type_name="internal";
            $paymentHistory=$this->finance_model->get_paymentHistory($fromdate,$todate,$payment_type_name);
            $data=$this->load->view('finance/reports/onlinereport.payments.php',array("payments"=>$paymentHistory),true);            
        }
        if($report_type==4)
        {
            //PAYMENT CANCELLATION
            $paymentHistory=$this->finance_model->get_paymentHistory($fromdate,$todate);
            $data=$this->load->view('finance/reports/onlinereport.payments.php',array("payments"=>$paymentHistory),true);
        }
        if($report_type==5)
        {
            //INVOICES   DONE
            $invoicesPaymentsHistory    =$this->finance_model->get_invoicesPaymentHistory($fromdate,$todate);                                 
            $data=$this->load->view('finance/reports/onlinereport.invoices.php',array("invoicesPayments"=>$invoicesPaymentsHistory),true);
        }
        if($report_type==6)
        {
            //TRANSACTIONS  DONE
            //ALL TRANSACTIONS GOUP BY TRANS_NUM ~ PAYMENTS BY TRANS_NUM
            $transactionHistory =$this->finance_model->get_transactionHistory($fromdate,$todate);
            $paymentHistory     =$this->finance_model->get_paymentHistory($fromdate,$todate);
            $data=$this->load->view('finance/reports/onlinereport.transactions.php',array("transactions"=>$transactionHistory,"payments"=>$paymentHistory),true);
        }
        
        echo json_encode(array("success"=>true,"result"=>$data));
    } 
    //**************************************************************
    //*************** INVOICE DRAFT ********************************
    //**************************************************************
    public function json_get_draft_invoices()
    {
        $result=$this->finance_model->get_draft_invoices();
        foreach($result as $i=>$v)
        {
            $result[$i]["date_issued_display"]      =($result[$i]["date_issued"]!=null)?date('Y/m/d',strtotime($v['date_issued'])):null;
            $result[$i]["date_due_display"]         =($result[$i]["date_due"]!=null)?date('Y/m/d',strtotime($v['date_due'])):null;
            $result[$i]["created_on_display"]       =($result[$i]["created_on"]!=null)?date('Y/m/d',strtotime($v['created_on'])):null;
        }   
        $this->result->json_pag($result);    
    }
    public function json_generate_invoice_draft()
    {
        $currency_type_id       =$this->input->get_post("currency_type_id");
        $description            =$this->input->get_post("description");
        $title                  =$this->input->get_post("title");
              
        $result=$this->finance_model->generate_invoice_draft($currency_type_id,$description,$title);
        $this->result->success($result[0]["generate_invoice_draft"]);
    }
    public function json_update_invoice_draft()
    {
        $currency_type_id       =$this->input->get_post("currency_type_id");
        $description            =$this->input->get_post("description");
        $title                  =$this->input->get_post("title");
        $invoice_id             =$this->input->get_post("invoice_id");
              
        $result=$this->finance_model->update_invoice_draft($invoice_id,$currency_type_id,$description,$title);
        $this->result->success($result[0]["update_invoice_draft"]);
    }
    public function json_get_invoice_items_draft()
    {
        $invoice_id       =$this->input->get_post("invoice_id");                    
              
        $result=$this->finance_model->get_invoice_items_draft($invoice_id);
        $this->result->json_pag($result);        
    }
    public function json_add_invoice_item_draft()
    {
        $invoice_id                 =$this->input->get_post("invoice_id");
        $charge_type_id             =$this->input->get_post("charge_type_id");
        $charge_price               =floatval($this->input->get_post("charge_price"));
        $charge_cost                =floatval($this->input->get_post("charge_cost"));
        $quantity                   =$this->input->get_post("quantity");
        $invoice_item_description   =$this->input->get_post("invoice_item_description");
        $tax_applies                =$this->input->get_post("tax_applies");
                                                                                                             
        $result=$this->finance_model->add_invoice_item_draft($invoice_id,$charge_type_id,$invoice_item_description,$charge_price,$charge_cost,$quantity,$tax_applies);
        $this->result->success($result[0]["add_invoice_item_draft"]);    
    }
    public function json_delete_invoice_item_draft()
    {
        $invoice_item_id    =$this->input->get_post("invoice_item_id");                                   
        $tax_applies        =$this->input->get_post("tax_applies");
        
        
        $result=$this->finance_model->delete_invoice_item_draft($invoice_item_id,$tax_applies);
        $this->result->success($result[0]["delete_invoice_item_draft"]);        
    }
    public function json_delete_draft_invoice()
    {
        $invoice_id     =$this->input->get_post("invoice_id");                                               
        $result=$this->finance_model->delete_draft_invoice($invoice_id);
        $this->result->success( $result[0]["delete_draft_invoice"]);    
    }
    public function json_update_draftinvoice_item()
    {
        $invoice_item_id    =$this->input->get_post("invoice_item_id");
        $charge_cost        =$this->input->get_post("charge_cost");
        $charge_price       =$this->input->get_post("charge_price");
        $quantity           =$this->input->get_post("quantity");
        
        $result=$this->finance_model->update_draftinvoice_item($invoice_item_id,$charge_price,$charge_cost,$quantity);
        $this->result->success($result[0]["update_draftinvoice_item"]);
    }
    public function json_apply_tax_invoice_draft()
    {
        $invoice_id     =$this->input->get_post("invoice_id");
        $tax_applies    =$this->input->get_post("tax_applies");
        
        $result=$this->finance_model->apply_tax_invoice_draft($invoice_id,$tax_applies);
        $this->result->success($result[0]["apply_tax_invoice_draft"]);
    }
    public function json_get_applied_tax_status_invoice_draft()
    {
        $invoice_id     =$this->input->get_post("invoice_id");
        $result=$this->finance_model->get_applied_tax_status_invoice_draft($invoice_id);
        $this->result->success($result[0]["get_applied_tax_status_invoice_draft"]);    
    }
    public function json_final_save_invoice_draft()
    {
        $invoice_id         =$this->input->get_post("invoice_id");
        $issuerinfo_address =$this->input->get_post("issuerinfo_address");
        $issuerinfo_email   =$this->input->get_post("issuerinfo_email");
        $invoice_comment    =$this->input->get_post("invoice_comment");
        
        $result=$this->finance_model->final_save_invoice_draft($invoice_id,$issuerinfo_address,$issuerinfo_email,$invoice_comment);
        $this->result->success($result[0]["final_save_invoice_draft"]);        
    }
    
    public $entityIds   ='';
    public function getAllLowerLevelEntities($startParentId)
    {                                                      
        $source=$this->finance_model->getAllLowerLevelEntities();
        $this->getAllLowerLevelEntitiesRec($source,intval($startParentId));
        
        if($this->entityIds!='')
            $this->entityIds=substr($this->entityIds,0,strlen($this->entityIds)-1);
       return $this->entityIds;
    }
    private function getAllLowerLevelEntitiesRec($source,$parentEId)//DFS
    {                              
        foreach($source as $v)                              
            if(intval($v["parent_entity_id"])==intval($parentEId))
            {
                $this->entityIds.=($v["entity_id"].',');
                $this->getAllLowerLevelEntitiesRec($source,$v["entity_id"]);           
            }
    }
    //**************************************************************
    //**************** PAYMENT *************************************
    //**************************************************************
    
    
    //AFTER PAYMENT SCREEN POPS UP RETURNS BACK 
    
    //@ BACK-END    when changing domains, need to get current activeInfo to pass to new domain
    public function json_getActiveUserOrgEntity()
    {                                           
        $a_u_id     = $this->permissions_model->get_active_user();
        $a_o_id     = $this->permissions_model->get_active_org();
        $a_e_id     = $this->permissions_model->get_active_entity();                      
        
            
        if(!isset($a_u_id))
                $activeInfoParams=-1;
        else        
            $activeInfoParams=(string)$a_u_id.','.(string)$a_u_id.','.(string)$a_e_id.','.SYS_GATEWAY_URL;
                                                                             
        $this->result->success( $activeInfoParams);
    }
    //JUST FOR FRONT-END
    public function json_getActiveSysgatewayUrl()
    {                                                                        
        echo json_encode(array("success"=>true,"result"=>SYS_GATEWAY_URL));
    }
    public function json_Dev_or_Live()
    {                                                    
        echo json_encode(array("success"=>true,"result"=>SYS_STATE));
    }
    //GET IO FINAL PAGE
    public function json_get_io_final_form($result,$amount=null,$trans_tag=null,$x_MD5_Hash=null,$x_response_reason_text=null,$exact_ctr=null,$INTERAC_RESPONSEKEY=null,$INTERAC_LOGINID=null,$INTERAC_TRANSACTIONKEY=null)
    {
        $data=array
        (
            "result"                    =>$result                   ,
            "amount"                    =>$amount                   ,
            "trans_tag"                 =>$trans_tag                ,
            "x_MD5_Hash"                =>$x_MD5_Hash               ,
            "x_response_reason_text"    =>$x_response_reason_text   ,
            "exact_ctr"                 =>$exact_ctr                ,
            
            "INTERAC_RESPONSEKEY"       =>$INTERAC_RESPONSEKEY      ,
            "INTERAC_LOGINID"           =>$INTERAC_LOGINID          ,
            "INTERAC_TRANSACTIONKEY"    =>$INTERAC_TRANSACTIONKEY
        );            
        
        $paymentPage            =$this->load->view('/finance/finance.io.receipt.php',$data, false);
        echo $paymentPage; 
    }
    
    //GET IO PAYMENT PAGE
    public function json_get_io_payment_form(
            $apptoken                   ,
            //GENERAL
            $currency_type_id           ,
            $currency_type_name         ,
            $amount                     ,
            $payable_amount             ,
            //INVOICE-SPECIFIC  FROM BE
            $invoice_id                 ,
            //DEPOSIT-SPECIFIC  FROM FE
            $master_entity_id           ,
            $slave_entity_id            ,
            $charge_type_id             ,
            
            //NEED TO BE DEPRECIATED   NO DEPOSIT_ID ANY MORE
            $deposit_id                 ,
            
            $season_id                  ,
            $season_division_id         ,
            $description                ,
            $mode                       ,
            //lOGGED-IN INFO FOR LIVE CONNECTION
            $a_u_id                     ,
            $a_o_id                     ,
            $a_e_id                     ,
            $a_person_eid               =null//just for players
                               
    ) 
    {               
        //DECIDE ON PAYMENT SERVER
        $INTERAC_LOGINID        =INTERAC_LOGINID;
        $INTERAC_TRANSACTIONKEY =INTERAC_TRANSACTIONKEY;
        $INTERAC_RESPONSEKEY    =INTERAC_RESPONSEKEY;
        $INTERAC_URL            =INTERAC_URL;
         
        $data=array
        (
            //GENERAL
            "currency_type_id"      =>$currency_type_id     ,
            "currency_type_name"    =>$currency_type_name   ,
            "amount"                =>$amount               ,
            "payable_amount"        =>$payable_amount       ,
            //INVOICE-SPECIFIC  FROM BE
            "invoice_id"            =>$invoice_id           ,
            //DEPOSIT-SPECIFIC  FROM FE
            "master_entity_id"      =>$master_entity_id     ,
            "slave_entity_id"       =>$slave_entity_id      ,
            "charge_type_id"        =>$charge_type_id       ,
            "deposit_id"            =>$deposit_id           ,
            "season_id"             =>$season_id            ,
            "season_division_id"    =>$season_division_id   ,
            "description"           =>$description          ,
            "mode"                  =>$mode                 ,
            //lOGGED-IN INFO FOR LIVE CONNECTION
            "a_u_id"                =>$a_u_id               ,
            "a_o_id"                =>$a_o_id               ,
            "a_e_id"                =>$a_e_id               ,
            "a_person_eid"          =>$a_person_eid         ,
            
            //SETTINGS
            "x_currency_code"       =>$currency_type_name   ,
            "x_amount"              =>$amount               ,
            
            "INTERAC_LOGINID"        =>$INTERAC_LOGINID       ,
            "INTERAC_TRANSACTIONKEY" =>$INTERAC_TRANSACTIONKEY,
            "INTERAC_RESPONSEKEY"   =>$INTERAC_RESPONSEKEY  ,
            "INTERAC_URL"            =>$INTERAC_URL
        ); 
                 
        $paymentPage            =$this->load->view('/finance/finance.io.php',array("params"=>$data), false);
        echo $paymentPage;
    }

    //**************************************************************
    //******PAYMENT ACTIONS [INTERNAL/CREDITCARD/INTERAC]***********
    //***************** FRONT-END  /  BACK-END *********************
    //**************************************************************
    public function json_pay_invoice_internal()     //INTERNAL   PAYMENT INVOICE [BE]
    {
        $invoice_id         =$this->input->get_post("invoice_id");
        $amount             =$this->input->get_post("amount");
        $payable_amount     =$this->input->get_post("payable_amount");
        $currency_type_id   =$this->input->get_post("currency_type_id");
                                                                                                             
        $result=$this->finance_model->pay_invoice_internal($invoice_id,$amount,$currency_type_id);
        $this->result->success($result[0]["pay_invoice_internal"]);    
    }
    
    public function json_pay_direct_invoice_cc()    //CREDITCARD PAYMENT INVOICE [BE]
    {
        //INVOICE-SPECIFIC
        $invoice_id                 =$this->input->get_post("invoice_id");    
        
        //GENERAL
        $currency_type_id           =$this->input->get_post("currency_type_id");
        $payment_type_id            =$this->input->get_post("payment_type_id");
        $amount                     =$this->input->get_post("amount");
        $mode                       =$this->input->get_post("mode");    
        $a_u_id_sent                =$this->input->get_post("a_u_id");
        $a_o_id_sent                =$this->input->get_post("a_o_id");
        $a_e_id_sent                =$this->input->get_post("a_e_id");
        $a_person_eid_sent          =$this->input->get_post("a_person_eid");
        
        
        $cardname                   =$this->input->get_post("cardname");
        $cardnumber                 =$this->input->get_post("cardnumber");
        $cvv                        =$this->input->get_post("cvv");
        $expirymonth                =$this->input->get_post("expirymonth");
        $expiryyear                 =$this->input->get_post("expiryyear");
        $country                    =$this->input->get_post("country");
        $region                     =$this->input->get_post("region");
        $city                       =$this->input->get_post("city");
        $street                     =$this->input->get_post("street");
        $postalcode                 =$this->input->get_post("postalcode");
        
        
        $params                   =array
        (
            //DIRECT INVOICE SPECIFIC
            'invoice_id'          =>  $invoice_id       ,
            //GENERAL
            'currency_type_id'   =>   $currency_type_id ,
            'payment_type_id'    =>   $payment_type_id  , 
            'amount'             =>   $amount           ,
            'cardname'           =>   $cardname         ,
            'cardnumber'         =>   $cardnumber       ,
            'cvv'                =>   $cvv              ,
            'expirymonth'        =>   $expirymonth      ,
            'expiryyear'         =>   $expiryyear       ,
            'country'            =>   $country          ,
            'region'             =>   $region           ,
            'city'               =>   $city             ,
            'street'             =>   $street           ,
            'postalcode'         =>   $postalcode       ,
            'mode'               =>   $mode             ,
            'a_u_id_sent'        =>   $a_u_id_sent      ,  
            'a_o_id_sent'        =>   $a_o_id_sent      ,  
            'a_e_id_sent'        =>   $a_e_id_sent      ,    
            'a_person_eid_sent'  =>   $a_person_eid_sent
        );    
           
        $result=$this->finance_model->pay_invoice_direct_cc($params);                                                                    
        if($result["status"]==true)
        {
            //CHECK SYS PAYMENT ACTION IS SUCCESSFULL OR NOT
            if(intval($result["pay_invoice_direct"])>=1)
            {
               $currentPaymentId=$result["pay_invoice_direct"];
               $this->finance_model->EmailNotificationConfirmationInvoicePayment
               (
                    $params["invoice_id"]
                    ,$currentPaymentId
                    ,$params["amount"]
                    ,$params["a_u_id_sent"]
                    ,$params["a_o_id_sent"]
                    ,$params["a_e_id_sent"]
                ); 
                //echo "<script>self.window.location = '/index.php/finance/json_get_cc_final_form/'".var_dump($result)."</script>";    
                echo "<script>self.window.location = '/index.php/finance/json_get_cc_final_form/true'</script>";    
            } 
            //CACHING INTERNAL PAYMENT FAILIURE
            else
                echo "<script>self.window.location = '/index.php/finance/json_get_cc_final_form"
                .'/'.'EXTERNAL PAYMENT WAS SUCCESSFULL BUT INTERNAL PAYMENT OPERATION WITH PROBLEM'
                ."'</script>";  
        }   
        //CACHING EXTERNAL PAYMENT FAILIURE
        else
            //echo "<script>self.window.location = '/index.php/finance/json_get_cc_final_form/"./*$result["pay_invoice_direct"]*/var_dump($result)."'</script>";
            echo "<script>self.window.location = '/index.php/finance/json_get_cc_final_form/".$result["pay_invoice_direct"]."'</script>";
    }
    
    /**
    * the IFRAME of the registration view
    * $payment_token.'/'
 	*
    *   mode 
    *   /season_id
    * /login_token  
    *                       ; 
    * 
    */
    public function htmlGetCreditCardFormFrontEnd()           
    {                 
    	$args = func_get_args();
 
        $data=array
        ( 
            "paymentToken"      =>$args[0]  ,
            'mode'     			=>$args[1]//  ,  // T for team P for player
           // 'season_id' 		=>$args[2]   ,//not needed / used
            //'login_token' 		=>$args[3]   ,//not needed / used
        );
        //GET TEMP DEPOSIT ENTRY FROM PENDING PAYMENTS
        $valid_modes=array("T",'P');
        if(!in_array($data['mode'],$valid_modes))
        {
			
			echo "Payment cannot proceed: Invalid Mode: ".$data['mode'];
			return;
        }
        $tempSavedDepositEntry  = $this->finance_model->getTempSavedDepositEntry($data['paymentToken']);
 
        
        if(!isset($tempSavedDepositEntry[0]))
        {
			echo "Payment cannot proceed: Invalid Payment Token: ".$data['paymentToken'];
			return;
        }
        $tempSavedDepositEntry  = $tempSavedDepositEntry[0];                                            
        $form_data              = array_merge($tempSavedDepositEntry,$data);
 
        $paymentPage = $this->load->view('/finance/payment.cc.php',$form_data , false);
        echo $paymentPage;
    }

    /**
    * the submit action of the view payment.cc.php
    * 
    * the name of this method is wrong, there is NO json at all.  
    * 
    * it is html
    * 
    */
    public function json_pay_direct_deposit_cc()    //CREDITCARD PAY DEPOSIT DIRECT   [FE]
    { 
    	//hidden form inputs
        $paymentToken               = $this->input->get_post("payment_token");
        $mode             			= $this->input->get_post("mode"); 
        $payment_type_id            = $this->input->get_post("payment_type_id");//visa or mastercard , etc
        //CREDIT CARD INFO
        $cardname                   = $this->input->get_post("cardname");
        $cardnumber                 = $this->input->get_post("cardnumber");
        $cvv                        = $this->input->get_post("cvv");
        $expirymonth                = $this->input->get_post("expirymonth");
        $expiryyear                 = $this->input->get_post("expiryyear");
        $country                    = $this->input->get_post("country");
        $region                     = $this->input->get_post("region");
        $city                       = $this->input->get_post("city");
        $street                     = $this->input->get_post("street");
        $postalcode                 = $this->input->get_post("postalcode");
 
        
        /* FETCH INFORMATION BASED ON TOKE_PAYMENT */
        $pendingPaymentInfo = $this->finance_model->getTempSavedDepositEntry($paymentToken);
        $pendingPaymentInfo = $pendingPaymentInfo[0]; 
       // var_dump($pendingPaymentInfo);
        $amount             = $pendingPaymentInfo["amount"];
        $currency_type_id   = $pendingPaymentInfo["currency_type_id"];
        $slave_entity_id    = $pendingPaymentInfo["slave_entity_id"];
        $master_entity_id   = $pendingPaymentInfo["master_entity_id"];
        $charge_type_id     = $pendingPaymentInfo["charge_type_id"];
        $user_id            = $pendingPaymentInfo["user_id"];
        $a_o_id             = $pendingPaymentInfo["owned_by"];
        $user_entity_id     = $pendingPaymentInfo["entity_id"];
        $user_entity_id     = $pendingPaymentInfo["entity_id"];
        $season_id    		= $pendingPaymentInfo["season_id"];
        $season_division_id = $pendingPaymentInfo["season_division_id"];
        
        
        $params = array
        ( 
            'paymentToken'      =>   $paymentToken      ,
            'cardname'          =>   $cardname          ,
            'cardnumber'        =>   $cardnumber        ,
            'cvv'               =>   $cvv               ,
            'expirymonth'       =>   $expirymonth       ,
            'expiryyear'        =>   $expiryyear        ,
            'country'           =>   $country           ,
            'region'            =>   $region            ,
            'city'              =>   $city              ,
            'street'            =>   $street            ,
            'postalcode'        =>   $postalcode        ,
            
            
            'amount'            =>   $amount            ,
            'slave_entity_id'   =>   $slave_entity_id   ,
            'master_entity_id'  =>   $master_entity_id  ,
            'mode'              =>   $mode              ,
            'currency_type_id'  =>   $currency_type_id  ,
            'payment_type_id'   =>   $payment_type_id   ,
            'charge_type_id'    =>   $charge_type_id    ,
            'user_id'           =>   $user_id
        ); 
           
        $result_str = 'false';
        $failure    = '';
        if($mode=="T")                                                                                                                                               
        {
            $result  =$this->finance_model->pay_deposit_direct_cc($params);
            if($result["status"] == true)                                                                         
            {   
                $currentMixDepositPaymentIds = explode(',',$result["pay_deposit_direct"]);//depositId-paymentId
                $depositId = $currentMixDepositPaymentIds[0];
                $paymentId = $currentMixDepositPaymentIds[1];
                
                $this->finance_model->assign_season_deposit($season_id,$depositId,$slave_entity_id,$master_entity_id,$amount);
                $result_str = 'true'; 
            }
            else
            {
                $failure = $result["pay_deposit_direct"]; 
                $result_str='false';
			}
            
        }
        else if($mode=="P")
        {
            $result = $this->finance_model->pay_deposit_direct_cc($params);
             // var_dump($result);  
            if($result["status"]==true)                                                                         
            {
                $currentMixDepositPaymentIds=explode(',',$result["pay_deposit_direct_player"]);//depositId-paymentId
                $depositId = $currentMixDepositPaymentIds[0];
                $paymentId = $currentMixDepositPaymentIds[1];
                
                $this->finance_model->record_player_payment($master_entity_id ,$slave_entity_id,$amount ,$currency_type_id  ,$payment_type_id ,$season_id,$season_division_id,$depositId);    
                $result_str = 'true';
            }
            else
            {
 				
		        
                $failure = $result["pay_deposit_direct_player"];
                $result_str = 'false';
			}
        }
        
        //REGARDELSS OF MODE OR SUCCESS
        //we end up in the same place
 
        $final_view_args = array($result_str,$amount,$currency_type_id,$failure);
		 
 
        $script_location = "<script>self.window.location = '/index.php/finance/json_get_cc_final_form/";
        $script_end = "'</script>";
        //piece teh view arguments together with slashes between for url passing
        $script_location .= implode('/',$final_view_args);//.'/'.$json_failure;
        $script_location .= $script_end;
                                                                                                      
  		echo $script_location;//redirect
        
        if($result_str=='true')
        {
        	//send payment receipt
	        $this->finance_model->EmailNotificationConfirmationDepositPayment
	        (
	             $depositId//DepositId
	            ,$paymentId//PaymentId
	            ,$amount
	            ,$user_id
	            ,$a_o_id
	            ,$slave_entity_id
	            ,$user_entity_id
	        );
			
        }
 
    }
    
    /**
    * GET CC FINAL PAGE
    * 
    * this is NOT json it is HTML
    * 
    * @param mixed $result
    */
    public function json_get_cc_final_form($result,$amt,$currency_type_id=null,$failure=null)//
    { 
        $data = array("result"=>$result,'amt'=>$amt,'currency_id'=>$currency_type_id,'failure'=>$failure);   
        $this->load->view('/finance/payment.cc.result.php',$data, false);
        
    }        	    
    
    public function json_pay_direct_invoice_db()    //INTERAC    PAYMENT INVOICE [BE]
    {
        if($this->input->get_post("Transaction_Approved")!='YES')
        {
            echo '<h1>Transaction Declined</h1>';
            return;
        }
        //INVOICE-SPECIFIC
        $invoice_id                 =$this->input->get_post("invoice_id");    
        //GENERAL
        $currency_type_id           =$this->input->get_post("currency_type_id");
        $payment_type_id            =$this->input->get_post("payment_type_id");
        $amount                     =$this->input->get_post("amount");  
        $mode                       =$this->input->get_post("mode");    
        $a_u_id_sent                =$this->input->get_post("a_u_id");
        $a_o_id_sent                =$this->input->get_post("a_o_id");
        $a_e_id_sent                =$this->input->get_post("a_e_id");
        $a_person_eid_sent          =$this->input->get_post("a_person_eid");
        
        $auth_num                   =$this->input->get_post("x_auth_code");
        $trans_tag                  =$this->input->get_post("x_trans_id");
        $x_MD5_Hash                 =$this->input->get_post('x_MD5_Hash');
        $exact_ctr                  =$this->input->get_post('exact_ctr');
        $x_response_reason_text     =$this->input->get_post('x_response_reason_text');
        
        $INTERAC_LOGINID            =$this->input->get_post('INTERAC_LOGINID');
        $INTERAC_TRANSACTIONKEY     =$this->input->get_post('INTERAC_TRANSACTIONKEY');
        $INTERAC_RESPONSEKEY        =$this->input->get_post('INTERAC_RESPONSEKEY');
        $INTERAC_URL                =$this->input->get_post('INTERAC_URL');
        
        $params                     =array
        (
            //DIRECT INVOICE SPECIFIC
            'invoice_id'            =>  $invoice_id             ,
            //GENERAL
            'currency_type_id'      =>   $currency_type_id      ,
            'payment_type_id'       =>   $payment_type_id       , 
            'amount'                =>   $amount                ,
            
            'mode'                  =>   $mode                  ,
            'a_u_id_sent'           =>   $a_u_id_sent           ,  
            'a_o_id_sent'           =>   $a_o_id_sent           ,  
            'a_e_id_sent'           =>   $a_e_id_sent           ,
            'a_person_eid_sent'     =>   $a_person_eid_sent     ,
            
            'auth_num'              =>   $auth_num              ,
            'trans_tag'             =>   $trans_tag             ,
            "INTERAC_LOGINID"       =>   $INTERAC_LOGINID       ,
            "INTERAC_TRANSACTIONKEY"=>   $INTERAC_TRANSACTIONKEY,
            "INTERAC_RESPONSEKEY"   =>   $INTERAC_RESPONSEKEY   ,
            "INTERAC_URL"           =>   $INTERAC_URL
        );    
                
        $result=$this->finance_model->pay_invoice_direct_db($params);
                             
        if($result["status"]==true)                                                                         
        {
            //CHECK SYS PAYMENT ACTION IS SUCCESSFULL OR NOT
            if(intval($result["pay_invoice_direct"])>=1)
            {
               $currentPaymentId=$result["pay_invoice_direct"];
               $this->finance_model->EmailNotificationConfirmationInvoicePayment($params["invoice_id"],$currentPaymentId,$params["amount"]
                    ,$params["a_u_id_sent"]
                    ,$params["a_o_id_sent"]
                    ,$params["a_e_id_sent"]
                );
                echo "<script>self.window.location = '/index.php/finance/json_get_io_final_form/true"
                .'/'.$amount               
                .'/'.$trans_tag            
                .'/'.$x_MD5_Hash           
                .'/'.$x_response_reason_text
                .'/'.base64_encode($exact_ctr)
                .'/'.$INTERAC_RESPONSEKEY   
                .'/'.$INTERAC_LOGINID       
                .'/'.$INTERAC_TRANSACTIONKEY
                ."'</script>";
            }
            //CACHING INTERNAL PAYMENT FAILIURE
            else                     
                echo "<script>self.window.location = '/index.php/finance/json_get_io_final_form"
                .'/'.'EXTERNAL PAYMENT WAS SUCCESSFULL BUT INTERNAL PAYMENT OPERATION WITH PROBLEM'
                ."'</script>";
                
        }                                      
        else
            //CACHING EXTERNAL PAYMENT FAILIURE
            echo "<script>self.window.location = '/index.php/finance/json_get_io_final_form"
            .'/'.$result["pay_invoice_direct"]
            ."'</script>";
    }
    
    
    public function json_pay_direct_deposit_db()    //INTERAC    PAYMENT INVOICE [FE]
    {
        if($this->input->get_post("Transaction_Approved")!='YES')
        {
            echo '<h1>Transaction Declined</h1>';
            return;
        }
        //DEPOSIT-SPECIFIC
        $master_entity_id           =$this->input->get_post("master_entity_id");
        $charge_type_id             =$this->input->get_post("charge_type_id");
        $slave_entity_id            =$this->input->get_post("slave_entity_id");
        $season_id                  =$this->input->get_post("season_id");
        $season_division_id         =$this->input->get_post("season_division_id");
        //GENERAL
        $currency_type_id           =$this->input->get_post("currency_type_id");
        $payment_type_id            =$this->input->get_post("payment_type_id");
        $amount                     =$this->input->get_post("amount");  
        $mode                       =$this->input->get_post("mode");    
        $a_u_id_sent                =$this->input->get_post("a_u_id");
        $a_o_id_sent                =$this->input->get_post("a_o_id");
        $a_e_id_sent                =$this->input->get_post("a_e_id");
        $a_person_eid_sent          =$this->input->get_post("a_person_eid");
        
        $auth_num                   =$this->input->get_post("x_auth_code");
        $trans_tag                  =$this->input->get_post("x_trans_id");
        $x_MD5_Hash                 =$this->input->get_post('x_MD5_Hash');
        $exact_ctr                  =$this->input->get_post('exact_ctr');
        $x_response_reason_text     =$this->input->get_post('x_response_reason_text');
        
        $INTERAC_LOGINID            =$this->input->get_post('INTERAC_LOGINID');
        $INTERAC_TRANSACTIONKEY     =$this->input->get_post('INTERAC_TRANSACTIONKEY');
        $INTERAC_RESPONSEKEY        =$this->input->get_post('INTERAC_RESPONSEKEY');
        $INTERAC_URL                =$this->input->get_post('INTERAC_URL');
        
        $params                     =array
        (
            //DIRECT DEPOSIT SPECIFIC
            'master_entity_id'      =>  $master_entity_id       ,
            'charge_type_id'        =>  $charge_type_id         ,
            'slave_entity_id'       =>  $slave_entity_id        ,
            'season_id'             =>  $season_id              ,
            'season_division_id'    =>  $season_division_id     ,
            //GENERAL
            'currency_type_id'      =>   $currency_type_id      ,
            'payment_type_id'       =>   $payment_type_id       , 
            'amount'                =>   $amount                ,
            
            'mode'                  =>   $mode                  ,
            'a_u_id_sent'           =>   $a_u_id_sent           ,  
            'a_o_id_sent'           =>   $a_o_id_sent           ,  
            'a_e_id_sent'           =>   $a_e_id_sent           ,
            'a_person_eid_sent'     =>   $a_person_eid_sent     ,
            
            'auth_num'              =>   $auth_num              ,
            'trans_tag'             =>   $trans_tag             ,
            "INTERAC_LOGINID"       =>   $INTERAC_LOGINID       ,
            "INTERAC_TRANSACTIONKEY"=>   $INTERAC_TRANSACTIONKEY,
            "INTERAC_RESPONSEKEY"   =>   $INTERAC_RESPONSEKEY   ,
            "INTERAC_URL"           =>   $INTERAC_URL
        );    
                
        
        if($mode=="T")                                                                                                                                               
        {
            $result=$this->finance_model->pay_deposit_direct_db($params);
            if($result["status"]==true)                                                                         
            {
                $currentMixDepositPaymentIds=explode(',',$result["pay_deposit_direct"]);//depositId-paymentId
                $depositId=$currentMixDepositPaymentIds[0];
                $paymentId=$currentMixDepositPaymentIds[1];
                 
                $this->finance_model->assign_season_deposit($season_id,$depositId,$slave_entity_id,$master_entity_id,$amount);
                $this->finance_model->EmailNotificationConfirmationDepositPayment
                (
                     $depositId//DepositId
                    ,$paymentId//PaymentId
                    ,$params["amount"]
                    ,$params["a_u_id_sent"]
                    ,$params["a_o_id_sent"]
                    ,$params["a_e_id_sent"]
                );
                echo "<script>self.window.location = '/index.php/finance/json_get_io_final_form/true"
                .'/'.$amount               
                .'/'.$trans_tag            
                .'/'.$x_MD5_Hash           
                .'/'.$x_response_reason_text
                .'/'.base64_encode($exact_ctr)
                .'/'.$INTERAC_RESPONSEKEY   
                .'/'.$INTERAC_LOGINID       
                .'/'.$INTERAC_TRANSACTIONKEY
                ."'</script>";   
            }
            else
                echo "<script>self.window.location = '/index.php/finance/json_get_io_final_form"
                .'/'.$result["pay_deposit_direct"]
                ."'</script>";
            
        }
        if($mode=="P")
        {
                $result=$this->finance_model->pay_deposit_direct_db($params);
                
                if($result["status"]==true)                                                                         
                {
                    $currentMixDepositPaymentIds=explode(',',$result["pay_deposit_direct_player"]);//depositId-paymentId
                    $depositId=$currentMixDepositPaymentIds[0];
                    $paymentId=$currentMixDepositPaymentIds[1];
                    
                    $this->finance_model->record_player_payment($master_entity_id ,$slave_entity_id,$amount ,$currency_type_id  ,$payment_type_id ,$season_id,$season_division_id,$paymentId);    
                    $this->finance_model->EmailNotificationConfirmationDepositPayment
                    (
                         $depositId//DepositId
                        ,$paymentId//PaymentId
                        ,$params["amount"]
                        ,$params["a_u_id_sent"]
                        ,$params["a_o_id_sent"]
                        ,$params["a_e_id_sent"]
                        ,$params["a_person_eid_sent"]
                    );
                    echo "<script>self.window.location = '/index.php/finance/json_get_io_final_form/true"
                    .'/'.$amount               
                    .'/'.$trans_tag            
                    .'/'.$x_MD5_Hash           
                    .'/'.$x_response_reason_text
                    .'/'.base64_encode($exact_ctr)
                    .'/'.$INTERAC_RESPONSEKEY   
                    .'/'.$INTERAC_LOGINID       
                    .'/'.$INTERAC_TRANSACTIONKEY
                    ."'</script>";  
                }                                                                       
                else
                    echo "<script>self.window.location = '/index.php/finance/json_get_io_final_form"
                    .'/'.$result["pay_deposit_direct_player"]
                    ."'</script>";
            }
    }
    
    //**************************************************************
    //*****************CANCELLATION*********************************
    //**************************************************************
    //NOTE :                                                           
    //PAYMENT AND INVOICE CANCELLATION WONT USER EACH OTHERS AT SERVER-SIDE
    public function json_invoicePaymentsCancellationPossibility()
    {
        $invoice_id         =$this->input->get_post("invoice_id");
        
        $result=$this->finance_model->invoicePaymentsCancellationPossibility($invoice_id);
        $this->result->success(   $result[0]["invoicePaymentsCancellationPossibility"]);                     
    }
    public function json_cancel_invoice()
    {
        $invoice_id         =$this->input->get_post("invoice_id");                                               
        
        $result=$this->finance_model->cancel_invoice($invoice_id);
        $this->result->success(   $result[0]["cancel_invoice"]);                     
    }
    public function json_cancel_payment()           //Goes to branches cc/db   
    {
        //db
        $auth_num       =$this->input->get_post("auth_num");
        $trans_tag      =$this->input->get_post("trans_tag");
        
        //cc            
        $TxRefNum       =$this->input->get_post("txrefnum");
        $OrderID        =$this->input->get_post("orderid");
        
        $payment_id     =$this->input->get_post("payment_id");
        $payment_type_id=$this->input->get_post("payment_type_id");
        $amount         =$this->input->get_post("amount"); 
                
        $payment_on     =$this->input->get_post("payment_on");
        
        
        $result=$this->finance_model->cancel_payment($payment_id,$payment_type_id,$auth_num,$trans_tag,$TxRefNum,$OrderID,$amount,$payment_on);
        $this->result->success(  $result[0]["cancel_payment"]); 
    }
    
    //**************************************************************
    //*****************ADD & HANDLE CURRENCIES************************
    //**************************************************************
    
    public function post_owned_currency()
    {
        $entity_id      =(int)$this->input->get_post('owner_entity_id');  
        $type_id        =(int)$this->input->get_post('type_id');  
        $type_code      =$this->input->get_post('type_code');  
        $desc           =$this->input->get_post('type_descr');  
        $abr            =$this->input->get_post('currency_abbrev');  
        $html           =$this->input->get_post('html_character');  
        $icon           =$this->input->get_post('icon');  
        
        $a_e_id         = $this->permissions_model->get_active_entity();                      
        
        //GET ALL ACTIVE LOWER LEVEL ENTITIES ID [COMMA BASED]
        $this->getAllLowerLevelEntities($a_e_id);
        $lowerLevelsEntities=$this->entityIds;
        $result                 =   $this->finance_model->update_lu_currency($type_id,$type_code,$abr,$desc,$html,$icon,$entity_id,$lowerLevelsEntities);
       
       $this->result->success( $result);
       
    }
    public function post_delete_owned_currency()
    {
        $type_id=(int)$this->input->get_post('type_id');  
        echo $this->finance_model->delete_currency($type_id);
    }
    
    //**************************************************************
    //*********************** EMAIL CONFIRMATION METHODS ***********
    //**************************************************************
    public function postEmailPaymentReceipt()
    {
        // read post
        $email        = $this->input->post('email');
        $items        = $this->input->post('items');
        $totals        = $this->input->post('totals');
        $gateway    = $this->input->post('gateway');
        $to            = $this->input->post('to');
        $from        =@$this->input->post('from');
        $test         =@$this->input->post('test');
        
        // defaults
        if(!$from) $from = 1;
        if(!$test) $test = false;
        
        $this->emailPaymentReceipt($email, $items, $totals, $gateway, $to, $from, $test);
    }
    public function postEmailInvoice()
    {
        // read post
        $email        = $this->input->post('email');
        $invoice    = $this->input->post('invoice');
        $items        = $this->input->post('items');
        $totals        = $this->input->post('totals');
        $to            = $this->input->post('to');
        $from        =@$this->input->post('from');
        $test         =@$this->input->post('test');
        
        // defaults
        if(!$test) $test = false;
        
        $this->emailInvoice($email, $invoice, $items, $totals, $to, $from, $test);
    }
    public function postEmailPaymentNotification()
    {
        echo "-- postEmailPaymentNotification --";
        
        // read post
        $email        = $this->input->post('email');
        $payment    = $this->input->post('payment');
        $invoice    = $this->input->post('invoice');
        $gateway    = $this->input->post('gateway');
        $to            = $this->input->post('to');
        $from        = $this->input->post('from');
        $test         =@$this->input->post('test');
        
        // defaults
        if(!$test) $test = false;
        
        $this->emailPaymentNotification($email, $payment, $invoice, $gateway, $to, $from, $test);
    }
    
    
    
    //**************************************************************
    //************************CHASE JOBS****************************
    //**************************************************************
    public function close_chase_batch()
    {
        $xml ="
            <?xml version=\"1.0\" encoding=\"UTF-8\"?>
            <Request>
            <EndOfDay> 
                <BIN>000002</BIN>
                <MerchantID>700000203090</MerchantID>
                <TerminalID>001</TerminalID>
            </EndOfDay>
            </Request>
        ";
        $eod=$this->finance_model->curl_to_chase($xml);
        return $eod;
        
    }
    
    
    
    
    
    
    
    
    
	/**
    * @author sam
    * only currencies OWNED BY given entity id
    * used in manage associations component
    * ignores inheritance and everythign else
    * this is NOT currencies theya re allowed to USE, that is different
    * 
    */
    public function json_entity_owned_currencies()
    {
        $entity_id=(int)$this->input->get_post('entity_id');  
        $this->result->json($this->finance_model->get_entity_owned_currencies($entity_id));
    }
    
    
    
    
    
    
    
    
    //**************************************************************
    //*********************BEANSTREM METHODS************************
    //**************************************************************
    public function test_beanstream_chron()
    {
		$type = $this->input->get_post('type');
		
		$test = false;
		
		switch($type)
		{
			case "c"://create
 				$this->cron_beanstream_create_batches($test);			
			break;
			case 'u'://upload
 				$this->cron_beanstream_upload_batch($test);	
			break;
			case "r"://report
 				$this->cron_beanstream_report($test);
			break;
			default:
				echo "failtype";
			break;
		}
    }
    
    
    
    public function test_eft_data()
    {
		$eft = $this->finance_model->get_eft();
		$items = $this->finance_model->get_eft_items();
		echo "finance.eft\n";
		echo $this->_print_table($eft);
		echo "\nfinance.eft_items\n";
		echo $this->_print_table($items);
    }
    
    private function _print_table($table)
    {
    	$first = true;
    	$str = "<table border=1>";
		foreach($table as $row)
		{
			if($first)
			{
				$first = false;
				$keys = array_keys($row);
				$str.= "<tr>";
				foreach($keys as $k)
				{
					$str.= "<th align=center>$k</th>";	
				}
				$str.= "</tr>";
			}
			$str.= "<tr>";
			foreach($row as $val)
			{
				if($val == null) $val = 'null';
				$str.= "<td align=center>$val</td>";	
			}
			$str.= "</tr>";
		}
    	$str .= "</table>";
    	return $str;
    }
    
    /**
	* send a request up to see check for reports back on these batches,  
	* will get a whole file as a report and then process it
	* 
	* called by chron
	*/
	public function cron_beanstream_report($test = false)
	{
		//echo "cron_beanstream_report";
		$this->load->library('beanstream');
		
		$batch_ids_plain = $this->finance_model->get_eft_batchids();
		 
		if(!count($batch_ids_plain))
		{
			if($test) {echo "zero batches are currently in state 'processed' so no reporting needed";}
			return;
		}
		$first_batch  = min($batch_ids_plain);
		$second_batch = max($batch_ids_plain); 
		$plain_params = $this->beanstream->make_curl_report_params($first_batch,$second_batch);
		 
		//append the URL with the url  arguments
		//$url=BEANSTREAM_REPORT_URL.http_build_query($params);
		$url = $this->beanstream->make_report_url($plain_params);
		
		// for some reason this wasnt working with CURL library, and this works as it is so i dont want to change it right now
		$ch = curl_init();
	    curl_setopt($ch, CURLOPT_HEADER, 0);
	    curl_setopt($ch, CURLOPT_VERBOSE, 0);
	    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	    curl_setopt($ch, CURLOPT_USERAGENT, "Mozilla/4.0 (compatible;)");
	    curl_setopt($ch, CURLOPT_URL, $url);
	  //  curl_setopt($ch, CURLOPT_POST, true); 
 
	    //curl_setopt($ch, CURLOPT_POSTFIELDS, $params); 
	    $curl_response = trim(curl_exec($ch));
	   // var_dump($curl_response); 
	    if($curl_response===false)
	    { 
			$error = curl_error($ch);
			 // echo "error";
			$plain_params['url'] = $post_url;
			$plain_params['curl_error'] = $error;
 
 			$msg = 'Batch Beanstream REPORT gathering failed on CURL ';
			$this->beanstream->email_error_report( $msg ,$plain_params);
			return;
	    }
	    else
	    {
 		
			$error = 'ErrorResponse';
			if(strstr($curl_response,$error))
			{ 
				//$xml = $this->beanstream->parse_csv_report_response($curl_response,$error);
 
				$plain_params['url'] = $post_url;
				$plain_params['error_response'] = $curl_response;
 
 				$msg = 'Batch Beanstream REPORT gathering returned an ErrorResponse ';
				$this->beanstream->email_error_report( $msg ,$plain_params);
				return;
			}
			else
			{ 
				$parsed = $this->beanstream->parse_csv_report_response($curl_response);
				 
				// we have one entry in this array per Batch
			 
				$spectrum_failed = 3;//id 4 is incomplete, i dont think its used right now
				$spectrum_success= 5;
				$PROCESSING = 2;
				$FAILED = 3;
				$COMPLETE = 5;
				
				//section 5.4 of bean_reporting.pdf
				foreach($parsed as $p)
				{ 
					$wid = $p['reference'];
					
					//status and state are on the entire batch. examine this first
					$batch_id = $p['batch_id'];
					$statusid = $p['status_id'];
					$stateid  = $p['state_id'];
					$stateMsg = $p['state_name'];
					$w_id     = $p['reference'];
					$m_ids    = $p['message_id'];
					$m_names  = $p['message_name'];
					$eft_id   = $this->finance_model->get_eftid_from_batchid($batch_id);
					$eft_item_id   = $this->finance_model->get_eftitemid_from_withdrawid($w_id);
					if($test)  	echo "$batch_id,$eft_item_id:".$stateid.$stateMsg.":".$statusid.$p['status_name'].":".$m_ids.$m_names."\n";
				   
					$internal_status = $PROCESSING;//assume this for now
					
					$charge_fees = false;//assume no fees charged. only happenes if it was cancelled in a certain way
 					
					if($test || $this->beanstream->is_status_failed($statusid)) 
					{					
						//echo 'testing fail states';
						$internal_status = $FAILED;	
						
						//ok this eft trans was rejected, find out if fees apply, etc , and do a rollback
						
						$this->finance_model->make_eft_reverse($eft_item_id);	 //dont do this yet
						
 
						$charge_fees = $this->beanstream->do_cancel_fees_apply($m_ids);
						 
						if($charge_fees)
						{
							//if($test)  	echo "apply fees  $eft_item_id";
							$this->finance_model->make_eft_apply_fees($eft_item_id);
						}
					}
					else
					{
						//the batch itself did not fail
						//but it may not be complete yet
						//echo "complete but stop now dont update yet ";return;
						if($this->beanstream->is_state_complete($stateid))
						{ 
							if($test)  	echo "complete!  $eft_item_id";
							$internal_status = $COMPLETE;
						}
						else
						{
							if($test)echo " $eft_item_id still pending\n";
						}
					}
					$this->finance_model->update_withdraw_status($w_id,$internal_status,$stateMsg.",".$m_names,$stateid);//2 is still pending
				}
			}
	    }
	    
	}
	
	/**
	* create as many batches as are needed for all the pending withdrawl/eft_items
	*/
	public function cron_beanstream_create_batches($test = false)
	{
		$this->load->library('beanstream'); 
		
    	$date = date('Y-m-d');
		$withdraws = $this->finance_model->get_withdraw_unprocessed_recent($date);
 
		if(count($withdraws)==0) 
		{
			if($test) {echo "Zero withdrawls are currently 'Pending', curl is finished";}
			return;
		}
		//base arrays for all batches
		$batches = array();
		$batch_eftitem_ids = array();
		//$batch_withdraw_ids = array();
		$file_paths = array();
		
		$batch_total = 0; 
		$first_time = true;
		
		foreach($withdraws as $w)
		{
			$amt = (float) $w['amount'];//this is not converted to pennies untill it is placed in the file_lineitem
			//do NOT use total, that is just a number that includes internal fees, they dont get that much, they only get amt
			if($first_time == true || $batch_total + $amt > BEANSTREAM_BATCH_MAX)
			{ 
				$first_time = false;
				$batch_total = 0;
				//if its our first time here, OR if its over batch limit
				//start a new batch
				$fileName = $this->beanstream->generate_eft_filename(BEANSTREAM_FILENAME_MAX,BEANSTREAM_FILENAME_PREFIX);
				$eft_id = $this->finance_model->insert_eft(1,1,$fileName,null,null);
				
				if($test) echo "NEW EFT_ID ".$eft_id;
				//create directory if it doesnt exist
				if(is_dir(DIR_EFT_BATCH) == false)
				{
					//echo getcwd();return;
					$msg = 'Batch EFT failed find the batch directory, is_dir failed ' ;
					$email_data = array();
					$email_data['error'] = $this->beanstream->get_error();
					$email_data['eft_id'] = $eft_id;
					$email_data['file_dir'] = DIR_EFT_BATCH;
					
					$this->beanstream->email_error_report( $msg ,$email_data);
					return;
				}
				
				
				$file_paths[$eft_id] = DIR_EFT_BATCH.$fileName;
				$batches[$eft_id] = array();
			}
			 
			// so whether it is a new or existing batch do the same thing:
			//udpate total, and add this line item to current batch
			$batch_total += $amt; 
			
			//each batch has one array of csv lineS. trim out whitespace on the edges just in case
			$batches[$eft_id][] = trim($w['csv']); //internally its also called 'remarks1' for some reason
			
			//each batch has multiple item ids  . 
			$batch_eftitem_ids[$eft_id][]  = (int)$w['eft_item_id']; 
		} 
	
		//if($test) {echo "\n num batches made:".count($batches);}
		unset($eft_id);
 		$PENDING = 1;
 		 
		foreach($file_paths as $eft_id=>$file_path)
		{
			//now create a physical file for EVERY atch 
			$created = $this->beanstream->make_eft_batch_file_lineitems($file_path,$batches[$eft_id]);
			
			if ( $created == false )
			{
				//file creation might have failed
				
				$msg = 'Batch EFT failed to create a the local file ' ;
				$email_data = array();
				$email_data['error'] = $this->beanstream->get_error();
				$email_data['batches'] = $batches[$eft_id];
				$email_data['file_path'] = $file_path;
				
				$this->beanstream->email_error_report( $msg ,$email_data);
				return;
			}
		
			foreach($batch_eftitem_ids[$eft_id] as $eft_item_id)
			{  
				//assign this eft item to the physical file  
				$this->finance_model->update_eft_item_file($eft_id,$eft_item_id);
			}
			
 			//update status of eft file itself to pending
			$this->finance_model->update_eft($eft_id,$PENDING);
			// END OF FILE CREATION !!!
			
		}
		if($test) { echo "created ".count($batches)." files"; }
	}
	
 
     /**  
     * this is called by the chron 
     * 
     * will grab any withdraw's not processsed yet, and 
     * create one EFT file for them and send it away
     * 
	* upload ONE single pending batch file, if any exist that are pending
     * 
     * @author SB
     * @return mixed
     */
	public function cron_beanstream_upload_batch($test = false)
	{ 
 		$PENDING = 1;
 		$PROCESSED = 2;
		$this->load->library('beanstream');
		$this->load->library('curl');
		$efts = $this->finance_model->get_eft_by_status($PENDING);
		if(!count($efts))
		{
			if($test) {echo "zero pending eft files in system";}
			return;
		}
		$eft = $efts[0];//just process the first one
 		$eft_id = $eft['eft_id'];
 		if($test) echo "process eft $eft_id";
 		
		$file_path = DIR_EFT_BATCH.$eft['file_name'];
		//get fresh copy of header params each time
	   
		$plain_params = $this->beanstream->make_curl_eft_params();
		
		//build url with params but before file is added 
 		$post_url= $this->beanstream->make_eft_url($plain_params);
 		 
		$params = $this->beanstream->add_file_to_params($plain_params,$file_path);
 
		$curl_response = $this->curl->simple_post($post_url,$params);
 
 		if($curl_response===false)
 		{
 			$plain_params['url'] = $post_url;
 			$plain_params['eft_id'] = $eft_id;
 			$curl_response = $this->curl->simple_error();
 			$msg = 'Batch EFT failed on CURL upload.'."\n file = ".$file_path."\n url = ".$post_url."\n";
			$this->beanstream->email_error_report( $msg ,$plain_params);
			return;
		} 
		
	 	$xml = $this->beanstream->parse_xml_eft_response($curl_response);
 
		 //extract turns an associative array into variables . opposite of list 
		extract($xml);
 
		//$this->_handle_beanstream_eft_response($code,$message,$batch_id,$params);
		$upload_success = $this->beanstream->handle_curl_batch_eft_response($code,$message,$batch_id,$params);
 
		if($upload_success === false)
		{
			//the email function just takes a Subject and an Array
			$arr = $eft;
			$arr['response_code']  = $code;
			$arr['message']        = $message;
			$arr['batch_id']       = $batch_id;
			$arr['params']   	   = $params;
			$arr['file_path']      = $file_path;
 			$this->beanstream->email_error_report('Batch EFT file has FAILED an attempt to upload. response code = "'.$code.'"',$arr);
 			//do not return; here, we still have to call update response at the end
		}
		else if ($upload_success === true)
		{
			if($test)  echo "SUCCESS!!! NEW BATCH = ".$batch_id;
			
 			//update status of eft file itself to processed , meaning upload success
			$this->finance_model->update_eft($eft_id,$PROCESSED);
			
			$this->_delete_local_file($file_path);
		}	
		else if($upload_success === -1)
		{
			if($test) echo "success was -1 so service busy : wait and try again later";
		}
		
		//for both success AND fail, always save the result  of the upload	
			// now update the eft record, assign the batch id , response code, message, etc		
		$r = $this->finance_model->update_eft_response($eft_id,$code,$message,$batch_id);
		//if($test)  echo $r; 
	}
    
    
    
    
    /**
    * delete local file
    * 
    * @param mixed $file_path
    */
    private function _delete_local_file($file_path)
    {
    	 if(SYS_STATE == "DEV") return true;//skip file delete for testing, we know it works
    	if(is_file($file_path))
    	{
    		//open permissions first. might be redundant, but go ahead anyway
    		chmod($file_path, FILE_WRITE_MODE);
			return unlink($file_path);
	    
    	}
    	else
    	{ 
			return false;
    	}
	}
    
    
    
    
    
    
    
    
    
    
  //SAMPLE CHASE REQUESTS
  public function Inquiry()
  {
        $xml ="
            <?xml version=\"1.0\" encoding=\"UTF-8\"?>
            <Request>
            <Inquiry> 
                <BIN>000002</BIN>
                <MerchantID>700000203090</MerchantID>
                <TerminalID>001</TerminalID>
                <OrderID>1317158305</OrderID>
                <InquiryRetryNumber>sdsa</InquiryRetryNumber>
            </Inquiry>
            </Request>
        ";
        $inquiry=$this->post_an_order($xml);
        echo '<pre>';
        echo var_dump($inquiry);
        echo '</pre>';
            
    }
  /*
     "<?xml version=\"1.0\" encoding=\"UTF-8\"?>
            
            <transRequest RequestCount=\"2\" >
            <batchFileID>
                <userID>bholbrook1</userID>
                <fileDateTime>".time()."</fileDateTime>
                <fileID>".time()."</fileID>
                <version>2.2</version>
            </batchFileID>

            
            
            <NewOrder BatchRequestNo=\"1\">
            <IndustryType>EC</IndustryType>
            <MessageType>FC</MessageType>
            <BIN>000002</BIN>
            <MerchantID>700000203090</MerchantID>
            <TerminalID>001</TerminalID>
            <CardBrand></CardBrand>
            <AccountNum>5500000000000004</AccountNum>
            <Exp>0112</Exp>
            <CurrencyCode>124</CurrencyCode>
            <CurrencyExponent>2</CurrencyExponent>            
            <AVSzip></AVSzip>
            <AVSaddress1></AVSaddress1>
            <AVSaddress2></AVSaddress2>
            <AVScity></AVScity>
            <AVSstate></AVSstate>
            <AVSphoneNum></AVSphoneNum>
            <AVSname></AVSname>            
            <OrderID>www".time()."</OrderID>
            <Amount>10</Amount>
            <Comments>Test Comment</Comments>
            </NewOrder>

            
            <!-- Batch Close (End of Day) -->
            <endOfDay BatchRequestNo=\"2\">
            <bin>000002</bin>
            <merchantID>700000203090</merchantID>
            <terminalID>001</terminalID>
            </endOfDay>
            </transRequest>
            "; 
    */
    public function json_PDFBuilder()
    {
        $content=$this->input->get_post("content");
        
        $buffer='<html><body>'.$content.'</body></html>';
                
        /*" <html >  
           ".date("Y-m-d H:i:s",time())."
            <img  height='500' width='500' src='1.png' />
            </html>
        ";*/
        $filename=time().".pdf";
        
        $this->load->library('pdf/html2fpdf');
        
        $pdf=new HTML2FPDF();
        $pdf->AddPage();           
        $pdf->WriteHTML($buffer);  
        
        $pdf->Output('tmp/'.$filename);
        
        /*$file = file_get_contents($filename);
        header('Content-Type: application/pdf');
        header("Content-Disposition: attachment; filename={$filename}");
        header('Content-Length: ' . strlen($file));
        echo $file;
        */                                                       
        echo json_encode(array("success"=>"true","result"=>$filename));    
    } 
    
}                                                
