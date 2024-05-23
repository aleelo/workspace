-- id,uuid,first_name,last_name,user_type,is_admin,role_id,login_type,email,password,
-- image,status,message_checked_at,client_id,notification_checked_at,is_primary_contact,job_title,disable_login,note,address,alternative_address,phone,
-- alternative_phone,dob,ssn,gender,passport_no,marital_status,emergency_name,emergency_phone,birth_date,birth_place,education_level,

-- education_field,education_school,sticky_note,skype,language,enable_web_notification,enable_email_notification,created_at,last_online,requested_account_removal,
-- deleted,department_id,job_title_en,job_title_so,employee_id

age_level,	
work_experience	,	
faculty	,
faculty2,	
place_of_work,	
bachelor_degree,	
master_degree,	
highest_school,	
relevant_document_url,

-- id,uuid,first_name,last_name,user_type,is_admin,role_id,login_type,email,password,
-- image,status,message_checked_at,client_id,notification_checked_at,is_primary_contact,job_title,disable_login,note,address,alternative_address,phone,
-- alternative_phone,dob,ssn,gender,passport_no,marital_status,emergency_name,emergency_phone,birth_date,birth_place,education_level,

-- education_field,education_school,sticky_note,skype,language,enable_web_notification,enable_email_notification,created_at,last_online,requested_account_removal,
-- deleted,department_id,job_title_en,job_title_so,employee_id

-- import user from temporary employee_import table
insert into rise_users
select 0 id,uuid(), concat(SPLIT_STR(FullName,' ',1),' ',SPLIT_STR(FullName,' ',2)) frist_name,SPLIT_STR(FullName,' ',3) last_name,'staff' staff, 0 isadmin, 5 roleid, 'azure_login' logintype, 'email@aleelo.com', '$2a$12$yULmdOlVi7E/ZBniI3k7luqr4e.bEncMD0sz59b8lPvDnoTl8xxam' password,
'' image, 'active' active, null massagechd, 0 cliendid, null notifched, 0 is_primary_contact, title, 0 disablelgn, null note, concat(District,' ', Area) address, 'AG3' altaddress, 252 phone,
null alternative_phone, null dob, null ssn,gender, null passportno,null maritalstatus,null emg,null emgphone,null birth_date, null placebirth,age age_level,WorkExperience work_experience,faculty,faculty2,PlaceofWork place_of_work,Bachelordegree bachelor_degree,Masterdegree master_degree , null degree,
null education_feild, PrimarySecondryandHigtschool school,HighestAcademicQualification highest_school,Resumeorrelevantdocuments relevant_document_url, null stickynote,null skype,'english' lang,1 enablenoti,1 enableemail,now(),now(),0 requestedaccount, 0 deleted,department deptid,Title titleeng,Title titleso,EmpolyeeID
from employee_import;

-- insert job info from rise users table
insert into rise_team_member_job_info

select 
0 id,	
id user_id	,	
now() date_of_hire,	
0 deleted	,	
0 salary	,	
0 salary_term,	
department_id department_id,	
0 section_id,	
job_title job_title_en,	
job_title job_title_so,	
'Fixed' employee_type,
employee_id
from rise_users 
where department_id = '3' AND alternative_address = 'AG3' ;

-- select * from rise_users limit 1;

-- update statement for first and last name
SELECT id, SPLIT_STR(first_name, ' ', 1) first, SPLIT_STR(first_name, ' ', 2) middle, SPLIT_STR(first_name, ' ', 3) last from rise_users 
where  SPLIT_STR(first_name, ' ', 2) <> '' and id > 1551


update rise_users
 set first_name = concat(SPLIT_STR(first_name, ' ', 1),' ', SPLIT_STR(first_name, ' ', 2)), 
last_name = SPLIT_STR(first_name, ' ', 3)
where  SPLIT_STR(first_name, ' ', 2) = '' and id > 1551

-- update email with first name and last name:
SELECT id, SPLIT_STR(first_name, ' ', 1) first, last_name last,concat(SPLIT_STR(first_name, ' ', 1),'.', last_name,'@revenuedirectorate.gov.so') email from rise_users 
where id > 1548

update rise_users
 set email = concat(SPLIT_STR(first_name, ' ', 1),'.', last_name,'@revenuedirectorate.gov.so')
where   id > 1548
