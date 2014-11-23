<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Untitled Document</title>
<style type="text/css">
fieldset {
	font-family: Courier New, Courier, monospace;
}
.selected {
	background-color: #CF0;
}
.small_code {
}
</style>
</head>

<body>
<strong>To change upload button size for content go to: uploader controller</strong>
<fieldset><legend></legend>
  <p><kbd>function content_file_uploadifier($allowed_file_types = '*.jpg;*.gif',$autostart_upload = TRUE)<br />
    {<br />
    $data['options'] = new stdClass();<br />
    // $data['options']-&gt;process_function = 'process_logo';<br />
    $data['options']-&gt;file_size_limit =3000000;<br />
    $data['options']-&gt;allowed_file_types = $allowed_file_types;<br />
    $data['options']-&gt;autostart_upload = $autostart_upload;<br />
  <br />
    $data['options']-&gt;on_add_params_handler = 'contentFileAddHandler'; //flagAddHandler';<br />
    $data['options']-&gt;on_success_handler = 'contentFileSuccessHandler';//'flagSuccessHandler';<br />
    $data['options']-&gt;on_cancel_handler = 'contentFileCancelHandeler';//'flagCancelHandeler';<br />
    $data['options']-&gt;on_error_handler = 'contentFileErrorHandeler';//'flagErrorHandeler';<br />
  <br />
    $button_image = TEMPLATELANGUAGEIMAGEPATH.'/upload-image.png';<br />
    $data['options']-&gt;text = $button_image;//ASSETSPATH.'images/upload-flag-button.png';<br />
    <span class="selected">$data['options']-&gt;buttonWidth = 80;<br />
  $data['options']-&gt;buttonHeight = 80; </span></kbd></p>
  <p><kbd> $view = $_SESSION['cms_template_system_path'].'uploadify/content_file_uploadifier';<br />
    $script = $_SESSION['cms_template_system_path'].'uploadify/js_content_file_uploadifier';<br />
    $output['html'] = $this-&gt;load-&gt;component_view($this,$view , $data, TRUE);<br />
    $output['script'] = $this-&gt;load-&gt;component_view($this,$script, $data, TRUE);<br />
    return $output;<br />
    }</kbd></p>
</fieldset>
<strong>Method to convert pagnation to ajax</strong>
<fieldset><legend></legend>
  <p><kbd>$data['pagination'] = ajax_pagination($this->pagination->create_links(),<span class="selected">'content_files'</span>); </kbd></p>
  <p><kbd>-instead of</kbd></p>
  <p><kbd>$data['pagination'] = $this-&gt;pagination-&gt;create_links();<hr />
  </kbd></p>
  <kbd><span class="selected">'content_files'</span></kbd> = function to call on click<br />
  function location helpers/pagination_helper.php
</fieldset>
</body>
</html>