<?php
class qa_html_theme extends qa_html_theme_base
{
	function body_content()
	{
		$this->body_prefix();
		$this->output('<DIV CLASS="qa-body-wrapper">', '');
		$this->header();
		$this->output('<DIV CLASS="qa-content-wrapper">', '');	// modxclub
		$this->sidepanel();
		$this->main();
		$this->output('</DIV> <!-- END content-wrapper -->');	// modxclub
		$this->footer();
		$this->output('</DIV> <!-- END body-wrapper -->');
		$this->body_suffix();
	}
	function search_field($search)
    {
        $this->output('<INPUT '.$search['field_tags'].' VALUE="'.@$search['value'].'" CLASS="qa-search-field" placeholder="جست و جو"/>');
    }
	function attribution()
	{
		// Please see the license at the top of this file before changing this link. Thank you.
		qa_html_theme_base::attribution();
		$this->output('<div class="qa-designedby"><span>Designed by <a href="http://www.tohid.ir.tc/" title="Towhid Nategheian">Towhid Nategheian</a>, </span></div>');
	}
	
	function page_title_error()//Plus1 had been added
		{
			$title=@$this->content['title'];
			$favorite=@$this->content['favorite'];
			
			if (isset($favorite))
				$this->output('<FORM '.$favorite['form_tags'].'>');
				
			$this->output('<H1>');
			
			if (isset($favorite)) {
				$this->output('<DIV CLASS="qa-favoriting" '.@$favorite['favorite_tags'].'>');
				$this->favorite_inner_html($favorite);
				$this->output('</DIV>');
			}
		
			if (isset($title))//Plus1 had been added here
			{
				$this->output($title);
				$this->output('<div style="float:right;display: inline-block;padding-left: 5px;padding-top: 5px;"><g:plusone annotation="none"></g:plusone></div>');
			}

			if (isset($this->content['error'])) {
				$this->output('</H1>');
				$this->error(@$this->content['error']);
			} else
				$this->output('</H1>');

			if (isset($favorite))
				$this->output('</FORM>');
		}
	
	function footer()
	{
		qa_html_theme_base::footer();
		$this->output('<script type="text/javascript" src="https://apis.google.com/js/plusone.js"></script>');    
	}
}

/*
	Omit PHP closing tag to help avoid accidental output
*/