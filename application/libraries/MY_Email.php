<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/*
 * PaperTiger: CodeIgniter's mailer (used by the expert pages, Affiliates.php and Dashboard.php) with one optional
 * setting for test copies, redirect_to (set only in config/development/email.php, never in production):
 * every email then goes to that one address, the real recipients are named in the subject
 * ("[TEST, for <to>, cc <cc>, bcc <bcc>] ...") and no copy (Cc/Bcc) goes to anyone else.
 * Without the setting every method behaves exactly as CI_Email.
 */
class MY_Email extends CI_Email {

	public $redirect_to = '';

	protected $_pt_to = array();
	protected $_pt_cc = array();
	protected $_pt_bcc = array();
	protected $_pt_subject = NULL;

	public function to($to)
	{
		if ($this->redirect_to === '')
		{
			return parent::to($to);
		}
		$this->_pt_to = $this->_str_to_array($to);
		parent::to($this->redirect_to);
		return $this->_pt_refresh_subject();
	}

	public function cc($cc)
	{
		if ($this->redirect_to === '')
		{
			return parent::cc($cc);
		}
		$this->_pt_cc = $this->_str_to_array($cc);
		return $this->_pt_refresh_subject();
	}

	public function bcc($bcc, $limit = '')
	{
		if ($this->redirect_to === '')
		{
			return parent::bcc($bcc, $limit);
		}
		$this->_pt_bcc = $this->_str_to_array($bcc);
		return $this->_pt_refresh_subject();
	}

	public function subject($subject)
	{
		if ($this->redirect_to === '')
		{
			return parent::subject($subject);
		}
		$this->_pt_subject = $subject;
		return $this->_pt_refresh_subject();
	}

	public function clear($clear_attachments = FALSE)
	{
		$this->_pt_to = $this->_pt_cc = $this->_pt_bcc = array();
		$this->_pt_subject = NULL;
		return parent::clear($clear_attachments);
	}

	protected function _pt_refresh_subject()
	{
		if ($this->_pt_subject !== NULL)
		{
			$for = implode(', ', $this->_pt_to);
			$for .= $this->_pt_cc ? ', cc '.implode(', ', $this->_pt_cc) : '';
			$for .= $this->_pt_bcc ? ', bcc '.implode(', ', $this->_pt_bcc) : '';
			parent::subject('[TEST, for '.$for.'] '.$this->_pt_subject);
		}
		return $this;
	}
}
