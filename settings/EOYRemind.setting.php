<?php

return array(
  'eoyremind_start' => array(
    'title' => 'EOY Remind: Start Date',
    'group_name' => 'EOY Remind',
    'group' => 'eoyremind',
    'name' => 'eoyremind_start',
    'type' => 'String',
    'html_type' => 'date',
    'default' => 'today',
    'is_domain' => 1,
    'is_contact' => 0,
    'description' => 'Start date for processing EOY reminder workflow.',
    'help_text' => '',
  ),
  'eoyremind_end' => array(
    'title' => 'EOY Remind: End Date',
    'group_name' => 'EOY Remind',
    'group' => 'eoyremind',
    'name' => 'eoyremind_end',
    'type' => 'String',
    'html_type' => 'date',
    'default' => 'today',
    'is_domain' => 1,
    'is_contact' => 0,
    'description' => 'End date for processing EOY reminder workflow.',
    'help_text' => '',
  ),
);
