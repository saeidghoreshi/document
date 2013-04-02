<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
   
require_once('schedule.php');
/**
* @author Sam
* controller for playoff schedules
* extends playoff controller
*/
class Tournament extends Schedule
{

	/**
    * 
    * @var $tournament_model
    */
    public $tournament_model;
    

	public function __construct()
    {
        parent::__construct();
        $this->load->model('tournament_model');
	}
	
	
	public function post_create_playoff()
	{
		
	}
	
	public function post_block()
	{
		
	}
	public function post_block_order()
	{
		
	}
	public function post_block_teams()
	{
		
	}
	public function json_blocks()
	{ 
	}
	public function post_block_group()
	{
		
	}
	public function json_block_groups()
	{
		
	}
	public function post_block_group_from_template()
	{
		
	}
	public function json_block_templates()
	{
		
	}
	public function post_create_template_from_group()
	{
		
	}
	public function post_assign_block_timeslot()
	{
		
	}
	public function post_remove_block_timeslot()
	{
		
	}
	public function post_block_rules()
	{
		
	}
	public function json_block_rules()
	{
		
	}
	public function post_block_group_rules()
	{
		
	}
	public function json_block_group_rules()
	{
		
	}
	public function json_build_block_brackets()
	{
		
	}
	
}
?>
