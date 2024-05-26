SELECT  lower(email) Username,first_name ,last_name ,first_name as 'Display name',j.job_title_so as 'Job title',d.nameSo Department,	
'252' as 'Office number',u.phone as 'Office phone',u.phone 'Mobile phone', null as Fax,	null as 'Alternate email address',u.address Address,
'Mogadishu' as City,'BN' as 'State or province','252' as 'ZIP or postal code','Somalia' as Country
FROM `rise_users` u
left join  rise_team_member_job_info j on j.user_id = u.id
left join  departments d on j.department_id = d.id

order by u.employee_id
