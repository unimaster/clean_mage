<?php 
class Magik_Backupsuite_Model_Cron {
    
const XML_PATH_SCHEDULE_AHEAD_FOR  = 'system/cron/schedule_ahead_for';
    
    public function profileschedule() {

	$schedules=Mage::getModel('cron/schedule')->getCollection()
                ->addFieldToFilter('status', Mage_Cron_Model_Schedule::STATUS_PENDING)
                ->load();
	$exists = array();
        foreach ($schedules->getIterator() as $txtschedule) {
            $exists[$txtschedule->getJobCode().'/'.$txtschedule->getScheduledAt()] = 1;
        }
    
	$profile_data = Mage::getModel('backupsuite/backupsuiteprofile')
    				 ->getCollection()
				->addFieldToFilter('cron_enable', array('eq' => 1));
	foreach($profile_data as $Alldata){
	   
		if($Alldata['cron_type']=='def'){
			$time_hour = $Alldata['cron_time_hour'];
			$time_minutes = $Alldata['cron_time_minutes'];
			$frequency =$Alldata['cron_time_frequency'];
			$frequencyDaily = Mage_Adminhtml_Model_System_Config_Source_Cron_Frequency::CRON_DAILY;
			$frequencyWeekly = Mage_Adminhtml_Model_System_Config_Source_Cron_Frequency::CRON_WEEKLY;
			$frequencyMonthly = Mage_Adminhtml_Model_System_Config_Source_Cron_Frequency::CRON_MONTHLY;
			$cronDayOfWeek = date('N');
			$cronExprArray = array(
						intval($time_minutes),                              # Minute
						intval($time_hour),                                 # Hour
						($frequency == $frequencyMonthly) ? '1' : '*',      # Day of the Month
						'*',                                                # Month of the Year
						($frequency == $frequencyWeekly) ? '1' : '*',       # Day of the Week
					);
				$cronExpr = join(' ', $cronExprArray);
		}else{      $cronExpr=$Alldata['cron_time_expression'];}

		$reqID=$Alldata['id'];
		$jobCode='mgkbackupsuite';
		$scheduleAheadFor = Mage::getStoreConfig(self::XML_PATH_SCHEDULE_AHEAD_FOR)*60;				
						
		$schedule = Mage::getModel('backupsuite/schedule');				
		$now = time();
		$timeAhead = $now + $scheduleAheadFor;
		$schedule->setJobCode($jobCode)
			 ->setCronExpr($cronExpr)
			 ->setStatus(Mage_Cron_Model_Schedule::STATUS_PENDING);

		for ($time = $now; $time < $timeAhead; $time += 60) {
			$ts = strftime('%Y-%m-%d %H:%M:00', $time);
			if (!empty($exists[$jobCode.'/'.$ts])) {
								    // already scheduled
								    continue;
			}
			if (!$schedule->trymgkSchedule($time,$reqID)) {
								    // time does not match cron expression
								    continue;
			}
			$schedule->unsScheduleId()->save();
		}

	}
    }
}