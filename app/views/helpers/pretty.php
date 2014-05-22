<?php
class PrettyHelper extends AppHelper{

	function date($date){
			return $this->prettyDate(new DateTime($date));			
	}
	
	function dateGantt($date){
		return $this->prettyDateShorter($date);
	}

	// Funciones PHP auxiliares - Modificadas respecto a la versión en Gantt
    function daysDifference($beginDate, $endDate ){
       //explode the date by "-" and storing to array
       $date_parts1=explode("-", $beginDate);
       $date_parts2=explode("-", $endDate);
       //gregoriantojd() Converts a Gregorian date to Julian Day Count
       $start_date=gregoriantojd($date_parts1[1], $date_parts1[2], $date_parts1[0]);
       $end_date=gregoriantojd($date_parts2[1], $date_parts2[2], $date_parts2[0]);
       return $end_date - $start_date;
    }

    function daysAgo($prev_date){
        return $this->daysDifference($prev_date, date("Y-m-d")); 
    }
    
    function prettyDate($date){		
        $days_ago = $this->daysAgo($date->format('Y-m-d'));
        if ($days_ago == 0)
            $diff_text = 'Hoy a las '.$date->format('H:i');
        else if ($days_ago == 1)
            $diff_text = 'Ayer a las '.$date->format('H:i');
        else if ($days_ago < 7){
			$dayOfWeek = $date->format('w');
			$dayNames = array('domingo', 'lunes', 'martes', 'miércoles', 'jueves', 'viernes', 'sábado');
			$dayName = $dayNames[$dayOfWeek];
            $diff_text = 'El '.$dayName.' pasado';
        }
		else if ($days_ago < 30) {
			$weeksAgo = floor($days_ago/7);
			if ($weeksAgo == 1){
				$diff_text = 'Hace una semana';
			} else {
				$diff_text = 'Hace '.$weeksAgo.' semanas';
			}
		}
		else if ($days_ago < 365) {
			$monthNames = array('', 'Enero', 'Febrero', 'Marzo', 'Abril', 'Mayo', 'Junio', 'Julio', 'Agosto', 'Septiembre', 'Octubre', 'Nomviembre', 'Diciembre');
			$monthNumber = $date->format('n');
			$dayNumber = $date->format('j');
			$monthName = $monthNames[$monthNumber];
			$diff_text = $dayNumber.' de '.$monthName;
		} else {
            $diff_text = $date->format('j-n-Y');    
		}
        return $diff_text;
    }
	
	    function prettyDateShorter($date){
        $days_ago = $this->daysAgo($date->format('Y-m-d'));
        if ($days_ago == 0)
            $diff_text = $date->format('H:i');
        else if ($days_ago == 1)
            $diff_text = 'Ayer';
        else if ($days_ago < 7)
            $diff_text = 'Hace '.$days_ago.' días';
        else if ($days_ago < 30)
            $diff_text = 'Hace '.floor($days_ago/7).' semanas';
        else 
            $diff_text = 'Hace '.floor($days_ago/30).' meses';    
        return $diff_text;
    }
	
	function size($size){
		if ($size <= 0){
			return '0 bytes';
		}
		$magnitude = floor(log($size, 1024));
		$magnitudeNames = array('bytes', 'KB', 'MB', 'GB', 'TB', 'PB', 'EB', 'ZB', 'YB', 'BB', 'GeoB');
		$prettySize = floor($size/pow(1024, $magnitude)).' '.$magnitudeNames[$magnitude];		
		return $prettySize;
	}

}
?>