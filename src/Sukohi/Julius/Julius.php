<?php namespace Sukohi\Julius;

use Carbon\Carbon;
use Illuminate\Support\Facades\HTML;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Request;
class Julius {

	private $base_dt, $event_callback = null;
	private $navigation_flag = true;
	private $day_of_week_flag = true;
	private $navigation_bar_flag = true;
	private $html, $navigation_js_function = '';
	private $mode = 'month';
	private $interval = '+30 minutes';
	private $hours, $events = [];
	private $day_labels = ['Sun', 'Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat'];
	private $classes = [
				'table' => '', 
				'header' => '', 
				'time' => '', 
				'prev' => '', 
				'next' => '', 
				'day_label' => '', 
				'today' => '', 
				'day' => ''
			];
	private $wraps = [
				'event' => ['', ''], 
				'day' => ['', ''], 
				'date' => ['', '']
			];
	private $month_labels = ['January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December'];
	private $icons = ['prev' => '&lt;', 'next' => '&gt;'];
	private $date_formats = ['year_month' => 'm Y', 'time' => 'g:ia'];

	public function __construct() {
		
		$this->base_dt = new Carbon();
		
	}
	
	public function make() {
		
		return new static();
		
	}
	
	public function generate() {
		
		$this->generateHeader();
		
		if($this->mode == 'month') {
				
			$this->generateBodyMonth();
				
		} else if($this->mode == 'week') {
				
			$this->generateBodyWeek();
				
		} else if($this->mode == 'day') {
				
			$this->generateBodyDay();
				
		}
		
		return $this->html;
		
	}
	
	public function setStartDate($date_time) {
		
		$this->base_dt = (empty($date_time)) ? Carbon::today() : Carbon::parse($date_time);
		return $this;
		
	}
	
	public function getStartDate() {
		
		return $this->base_dt;
		
	}
	
	public function showNavigationBar($boolean) {
		
		$this->navigation_bar_flag = $boolean;
		return $this;
		
	}
	
	public function showNavigation($boolean) {
		
		$this->navigation_flag = $boolean;
		return $this;
		
	}
	
	public function showDayOfWeek($boolean) {

		$this->day_of_week_flag = $boolean;
		return $this;
		
	}
	
	public function setMode($mode) {
		
		if(in_array($mode, ['month', 'week', 'day'])) {
			
			$this->mode = $mode;
			
		}
		
		return $this;
		
	}
	
	public function setHours($start_hour, $end_hour) {
		
		$this->hours = ['start' => $start_hour, 'end' => $end_hour];
		return $this;
		
	}
	
	public function setClasses($classes) {
		
		$this->classes = $classes;
		return $this;
		
	}
	
	public function setWraps($wraps) {
		
		$this->wraps = $wraps;
		return $this;
		
	}
	
	public function setIcons($icons) {
		
		$this->icons = $icons;
		return $this;
		
	}

    public function setNavigationJsFunction($js_function_name) {

        $this->navigation_js_function = $js_function_name;
        return $this;

    }
	
	public function setDayLabels($labels) {
		
		$this->day_labels = $labels;
		return $this;
		
	}
	
	public function setMonthLabels($labels) {
		
		$this->month_labels = $labels;
		return $this;
		
	}
	
	public function setEvents($events, $callback = null) {

		$this->events = $events;
		$this->event_callback = $callback;
		return $this;
		
	}
	
	public function setInterval($interval) {

		$this->interval = $interval;
		return $this;
		
	}
	
	public function setDateFormats($formats) {
		
		$this->date_formats = $formats;
		return $this;
		
	}
	
	private function generateHeader() {
		
		$year = $this->base_dt->year;
		$month = $this->month_labels[$this->base_dt->month - 1];
		$year_month = str_replace(['Y', 'm'], [$year, $month], $this->date_formats['year_month']);
		
		$html = '<table'. $this->generateClass($this->classes['table']) .'>';
		
		if($this->navigation_bar_flag) {
		
			$html .= '<thead>';
			$html .= '<tr'. $this->generateClass($this->classes['header']) .'>';
			
			if($this->mode == 'week' || $this->mode == 'day') {
				
				$html .= '<th>&nbsp;</th>';
				
			}
			
			if($this->navigation_flag) {
	
				$colspan = ($this->mode == 'day') ? 1 : 5;
				$html .= '<th>';
				$html .= $this->generateLink('prev');
				$html .= '</th>';
				$html .= '<th colspan="'. $colspan .'"'. $this->generateClass($this->classes['year_month']) .'>';
				$html .= $year_month;
				$html .= '</th>';
				$html .= '<th>';
				$html .= $this->generateLink('next');
				$html .= '</th>';
				
			} else {
				
				$html .= '<th colspan="7">'.  $year_month .'</th>';
				
			}
			
			$html .= '</tr>';
			$html .= '</thead>';
		
		}
		
		$html .= '<tbody>';
		
		if($this->mode != 'day' && $this->mode != 'week') {
			
			$html .= '<tr>';
		
			for($i = 0; $i < 7; $i++) {
				
				$html .= '<td'. $this->weekDayClass('day_label', $i) .'>'. $this->day_labels[$i] .'</td>';
				
			}
		
			$html .= '</tr>';
			
		}
		
		if($this->day_of_week_flag && 
				($this->mode == 'day' || $this->mode == 'week')) {
			
			$html .= $this->generateWeekLabels();
			
		}
		
		$this->html .= $html;
		
	}
	
	private function generateWeekLabels() {
		
		if($this->mode == 'week') {
			
			$day = $this->base_dt->addDay()->modify('last sunday')->day;
			$count = 7;
			
		} else if($this->mode == 'day') {
			
			$day = $this->base_dt->day;
			$count = 1;
			
		}
		
		$end_day = $this->base_dt
						->copy()
						->lastOfMonth()
						->day;
		
		$html = '<tr>';
		$html .= '<td'. $this->weekDayClass('day_label', $this->base_dt->format('w')) .'>&nbsp;</td>';
		
		for($i = 0; $i < $count; $i++) {
			
			if($this->mode == 'day') {
				
				$day_number = $this->base_dt->format('w');
				$col_span = 3;
				
			} else {
				
				$day_number = $i;
				$col_span = 1;
				
			}
			
			if($day > $end_day) {
				
				$day = 1;
				
			}

			$html .= '<td colspan="'. $col_span .'"'. $this->weekDayClass('day_label', $day_number) .'>';
			$html .= $this->day_labels[$day_number] . ' ';
			$html .= intval($day);
			$html .= '</td>';
			$day++;
			
		}
		
		$html .= '</tr>';
		return $html;
		
	}
	
	private function generateBodyMonth() {
		
		$dt = $this->base_dt->copy()->firstOfMonth();
		$start_week_day = ($dt->dayOfWeek == 0) ? 7 : $dt->dayOfWeek;
		$end_day = $dt->copy()->lastOfMonth()->day;
		$html = '<tr>';
		$i_count = ($start_week_day == 7) ? 1 : 0;
		
		for($i = $i_count; $i < 9; $i++) {
			
			for($j = 0; $j <= 6; $j++) {
			
				$today_class = ($dt->isToday()) ? $this->generateClass($this->classes['today']) : '';
				$html .= '<td data-datetime="'. $dt->format('Y-m-d') .'"'. $today_class .'>';
				$html .= $this->wraps['date'][0];
				
				if($dt->month == $this->base_dt->month && ($i > 0 || $j >= $start_week_day)) {

					$html .= $this->wraps['day'][0];
					$event = $this->generateEvents($dt, $dt->copy()->addDay());
					
					if($event == '&nbsp;') {
						
						$html .= '<div'. $this->weekDayClass('day', $j) .'>' . $dt->day .'</div>';
						
					} else {
						
						$html .= $event;
						
					}
					
					$html .= $this->wraps['day'][1];
					$dt->addDay();
					
				} else {
					
					$html .= '&nbsp;';
					
				}
				
				$html .= $this->wraps['date'][1];
				$html .= '</td>';
				
			}
			
			if($dt->month == $this->base_dt->month) {
				
				$html .= '</tr><tr>';
				
			} else {
				
				break;
				
			}
			
		}
		
		$html .= '</tr>';
		$html .= '</tbody>';
		$html .= '</table>';
		$this->html .= $html;
		
	}
	
	private function generateBodyWeek() {
		
		$html = '';
		$intervalMinutes = $this->getIntervalMinutes();

		$start_dt = new Carbon($this->base_dt->toDateString() .' '. $this->hours['start']);
		$end_dt = new Carbon($this->base_dt->toDateString() .' '. $this->hours['end']);
		$block_count = $start_dt->diffInMinutes($end_dt) / $intervalMinutes;

		for ($i = 0; $i < $block_count; $i++) {
			
			$dt = $start_dt->copy()->addMinutes($intervalMinutes * $i);
			$html .= '<tr>';
			$html .= '<td'. $this->generateClass($this->classes['time']) .'>';
			$html .= $dt->format($this->date_formats['time']);
			$html .= '</td>';
			
			for ($j = 0; $j < 7; $j++) {
				
				$html .= '<td data-datetime="'. $dt .'">';
				$html .= $this->wraps['date'][0];
				$html .= $this->generateEvents($dt, $dt->copy()->addMinutes($intervalMinutes));
				$html .= $this->wraps['date'][1];
				$html .= '</td>';
				$dt->addDay();
				
			}
				
			$html .= '</tr>';
			
		}
		
		$html .= '</tbody>';
		$html .= '</table>';
		$this->html .= $html;
		
	}
	
	private function generateBodyDay() {

		$html = '';
		$intervalMinutes = $this->getIntervalMinutes();
		
		$start_dt = new Carbon($this->base_dt->format('Y-m-d') .' '. $this->hours['start']);
		$end_dt = new Carbon($this->base_dt->format('Y-m-d') .' '. $this->hours['end']);
		$block_count = $start_dt->diffInMinutes($end_dt) / $intervalMinutes;
		
		for ($i = 0; $i < $block_count; $i++) {
			 
			$dt = $start_dt->copy()->addMinutes($intervalMinutes * $i);
			$html .= '<tr>';
			$html .= '<td'. $this->generateClass($this->classes['time']) .'>';
			$html .= $dt->format($this->date_formats['time']);
			$html .= '</td>';
			$html .= '<td colspan="3" data-datetime="'. $dt .'">';
			$html .= $this->wraps['date'][0];
			$html .= $this->generateEvents($dt, $dt->copy()->addMinutes($intervalMinutes));
			$html .= $this->wraps['date'][1];
			$html .= '</td>';
			$html .= '</tr>';
			 
		}
		
		$html .= '</tbody>';
		$html .= '</table>';
		$this->html .= $html;
		
	}
	
	private function getIntervalMinutes() {
		
		$dt = new Carbon('today '. $this->interval);
		$diff_minutes = $dt->diffInMinutes(Carbon::today());
		return $diff_minutes;
		
	}
	
	private function generateLink($direction) {
		
		$dt = $this->base_dt->copy();

		if($this->mode == 'week') {
		
			($direction == 'prev') ? $dt->modify('last sunday') : $dt->modify('next sunday');
		
		} else if($this->mode == 'day') {
		
			($direction == 'prev') ? $dt->subDay() : $dt->addDay();
		
		} else {

            $dt = ($direction == 'prev') ? $dt->modify('first day of previous month') : $dt->modify('first day of next month');

		}

		$base_date = ($this->mode == 'month') ? $dt->format('Y-m') : $dt->format('Y-m-d');
		$url = Request::url() .'?base_date='. $base_date .'&'. http_build_query(Input::except('base_date'));
		$class = (isset($this->classes[$direction])) ? $this->generateClass($this->classes[$direction]) : '';

        if(!empty($this->navigation_js_function)) {

            $url = '#';
            $onclick = ' onclick="return '. $this->navigation_js_function .'(\''. $base_date .'\');"';

        }

		return '<a id="julius_'. $direction .'_'. $this->mode .'" href="'. $url .'"'. $class .' data-date="'. $base_date .'"'. $onclick .'>'. $this->icons[$direction] .'</a>';
		
	}
	
	private function generateEvents($start_dt, $end_dt) {

		$html = '&nbsp;';
		
		if(empty($this->events)) {
			
			return $html;
			
		}

        $end_dt->subSecond();
        $callback = $this->event_callback;
		$events = [];

		foreach($this->events as $date => $event_values) {
			
			$event_dt = new Carbon($date);
			
			if($event_dt->between($start_dt, $end_dt)) {
				
				if($callback != null) {
					
					$events[$event_dt->toDateTimeString()] = $event_values;
					
				} else {
					
					foreach ($event_values as $event) {
						
						$html .= $this->wraps['event'][0];
						$html .= $event;
						$html .= $this->wraps['event'][1];
						
					}
					
				}
				
			}
			
		}

		if($callback != null && is_callable($callback) && !empty($events)) {

            $html = $callback($events, $start_dt, $end_dt);

		}
		
		return $html;
		
	}
	
	private function weekDayClass($class_name, $week_day_no) {
		
		if(!isset($this->classes[$class_name])) {
			
			return '';
			
		}
		
		$class = '';
				
		if(!is_array($this->classes[$class_name])) {
		
			$class = $this->generateClass($this->classes[$class_name]);
		
		} else if(isset($this->classes[$class_name][$week_day_no])) {

			$class = $this->generateClass($this->classes[$class_name][$week_day_no]);
		
		}
		
		return $class;
		
	}
	
	private function generateClass($class) {
		
		return (!empty($class)) ? ' class="'. $class .'"' : '';
		
	}

}