<?php
	class CalendarHelper extends AppHelper{
				
		function scripts(){
			App::Import('Helper', 'Html');
			$Html = new HtmlHelper();
			$str = $Html->script($this->webroot.'js/jscalendar/calendar.js');
			$str .= $Html->script($this->webroot.'js/jscalendar/lang/calendar-es.js');
			$str .= $Html->script($this->webroot.'js/jscalendar/calendar-setup.js');
			$str .= $Html->css($this->webroot.'js/jscalendar/skins/tiger/theme.css');
			return $str;
		}
		
		function set($id, $showsTime=null, $format=null, $onClose=null){
			if($format==null){
				$format="%d/%m/%Y";
			}
			if($onClose==null)
			{
				$onClose="null";
			}
            if ($showsTime == 'true')
			    $str = '<script type="text/javascript">
					      Calendar.setup(
						    {
						      inputField  : "'.$id.'",
						      ifFormat    : "'.$format.' %H:%M:%S",
						      timeFormat  : "24",
						      showsTime   : true,
						      firstDay    : 1,
							  onClose	  : '.$onClose.'
						    }
					      );
					    </script>';
            else
			    $str = '<script type="text/javascript">
					      Calendar.setup(
						    {
						      inputField  : "'.$id.'",
						      ifFormat    : "'.$format.'",
						      timeFormat  : "24",
						      showsTime   : false,
						      firstDay    : 1,
						      showOthers  : true,
							  onClose	  : '.$onClose.'
						    }
					      );
					    </script>';

			return $str;
		}
		
	}

?>
