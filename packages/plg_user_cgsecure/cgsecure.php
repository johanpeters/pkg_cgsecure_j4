<?php
/**
 * @component     Plugin User CG Secure
 * Version			: 2.4.1
 * @license http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
 * @copyright (C) 2023 ConseilGouz. All Rights Reserved.
 * @author ConseilGouz 
**/

// No direct access.
defined('_JEXEC') or die();
use Joomla\CMS\Factory;
use Joomla\CMS\Plugin\CMSPlugin;
use Joomla\CMS\Form\Form;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Log\Log;
use ConseilGouz\CGSecure\Helper\Cgipcheck;

class PlgUserCGSecure extends CMSPlugin
{
    public $myname='UserCGSecure';
	public $mymessage='Joomla User : try to access forms...';
	public $errtype = 'w';	 // warning 
	public $cgsecure_params;
    private $debug; 
	public function __construct(&$subject, $config)
	{
		parent::__construct($subject, $config);

		$this->cgsecure_params = Cgipcheck::getParams();
	}

	// more info on UserLoginFailure
    function onUserLoginFailure( $user)  {
        $this->debug = $this->cgsecure_params->debug;
        if (!$this->debug) return;
		Log::addLogger(array('text_file' => 'cgipcheck.trace.log'), Log::INFO);
		$cmd = $_SERVER['REQUEST_URI'];
		Log::add('Cmd : '.htmlspecialchars($cmd, ENT_QUOTES), Log::INFO, "Auth Fail");
		foreach($_REQUEST as $key => $value) {
		    Log::add('key : '.htmlspecialchars($key, ENT_QUOTES).":".htmlspecialchars($value, ENT_QUOTES), Log::INFO, "Auth Fail");
		}
	}
	// Check IP on prepare Forms
	function onContentPrepareForm(Form $form, $data)
	{
		$form_name = $form->getName();
		$name = explode('.',$form_name);
		$components = $this->cgsecure_params->components;
		$arr_components = explode(',',$components);
		if (!in_array($name[0], $arr_components))
		{
		    return true;
		}
		if (($name[0] == 'com_users')  && ($name[1] == 'profile') ) return true; // Contact forms call com_user.profile, so ignore this one
		$prefixe = $_SERVER['SERVER_NAME'];
		$prefixe = substr(str_replace('www.','',$prefixe),0,2);
		$this->mymessage = $prefixe.$this->errtype.'-'.$this->mymessage;

		Cgipcheck::check_ip($this,$this->myname.' : '.$form_name);
	}
}
