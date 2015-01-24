create database IF NOT EXISTS project;
use project;

drop table if exists voted;
drop table if exists tagged;
drop table if exists tag;
drop table if exists answer;
drop table if exists comment;
drop table if exists question;
drop table if exists user;

--
-- Table for user
--
create table user
(
	id int auto_increment,
	acronym varchar(12) unique not null,
	email varchar(80),
	name varchar(80),
	text varchar(255),
	questions int default 0,	
	password char(255),
	created datetime,
	updated datetime,
	deleted datetime,
	active datetime,
	primary key(id) 

) engine innodb character set utf8;

create table question
(
	id int auto_increment,
	user_id int,
	title varchar(80),
	question varchar(255),
	answers int default 0, 
	created datetime,
	updated datetime,
	deleted datetime,
	primary key (id),
	foreign key (user_id) references user (id)

) engine innodb character set utf8;

create table answer
(
	id int auto_increment,
	user_id int,
	question_id int,
	answer varchar(255),
	created datetime,
	updated datetime,
	deleted datetime,
	primary key(id),
	foreign key (question_id) references question (id),
	foreign key (user_id) references user (id)

) engine innodb character set utf8;

create table comment
(
	id int auto_increment,
	user_id int,
	post_id int,
	comment varchar(255),
	created datetime,
	updated datetime,
	deleted datetime,
	primary key(id),
	foreign key (user_id) references user (id)

) engine innodb character set utf8;


-- create table answer_answer
-- (
-- 	id int auto_increment,
-- 	user_id int,
-- 	answer_id int,
-- 	answer varchar(255),
-- 	votes int,
-- 	created datetime,
-- 	updated datetime,
-- 	deleted datetime,
-- 	active datetime,	
-- 	primary key(id),
-- 	foreign key (answer_id) references answer (id)
-- 
-- ) engine innodb character set utf8;

-- create table tag
-- (
-- 	tag varchar(80),
-- 	-- num int,
-- 	primary key (tag)
-- 
-- ) engine innodb character set utf8;

create table tag
(
	tag varchar(80),
	question_id int,
	primary key (tag, question_id),
	-- foreign key (tag) references tag (tag),
	foreign key (question_id) references question (id)

) engine innodb character set utf8;

create table voted
(
	user_id int,
	answer_id int,
	primary key (user_id, answer_id),
	foreign key (user_id) references user (id),
	foreign key (answer_id) references answer (id)

) engine innodb character set utf8;

-- delimiter //
-- create trigger update_tag_num 
-- after insert on tagged for each row begin
-- declare count int;
-- 	set count = (select count(*) from tagged where tag = new.tag);
-- 	update tag set num=count where tag = new.tag;
-- end;//
-- delimiter ;

delimiter //
create trigger questions_delete 
before delete on question for each row begin

delete from tag where question_id = old.id;
	
end;//
delimiter ;

delimiter //
create trigger questions_count 
after insert on question for each row begin

update user set questions = questions +1 where id = new.user_id;
	
end;//
delimiter ;

delimiter //
create trigger answers_count 
after insert on answer for each row begin

update question set answers = answers +1 where id = new.question_id;
	
end;//
delimiter ;

delimiter //
create trigger answers_count_delete 
after delete on answer for each row begin

update question set answers = answers -1 where id = old.question_id;
	
end;//
delimiter ;



-- delimiter //
-- create trigger questions_asked 
-- after insert on question for each row begin
-- 
-- update user set questions = questions + 1 where user.user_id = question.user_id;
-- 	
-- end;//
-- delimiter ;

-- delimiter //
-- create trigger tagged_delete 
-- after delete on tagged for each row begin
-- 
-- 	declare count int;
-- 
-- 	set count = (select count(*) from tagged group by tag);
-- 
-- 	if (count > 0) then 
-- 		update tag set num=count where tag = tag;
-- 	else 
-- 		delete from tag where tag = tag;
-- 	end if;
-- 
-- end;//
-- delimiter ;

-- delimiter //
-- create trigger tagged_delete 
-- after delete on tagged for each row begin
-- declare count int;
-- 	set count = (select count(*) from tagged where tag = tagged.tag);
-- 	if (count > 0) then 
-- 		update tag set num=count where tag = tagged.tag;
-- 	else 
-- 		delete from tag where tag = tagged.tag;
-- 	end if;
-- end;//
-- delimiter ;

-- select * from tag;

-- select * from tag;

select * from question;

select * from comment;

select * from user;

select * from tag;

select * from answer;

select tag from tag where question_id = 3;

select tag from tag where question_id = 4;

-- select tag from question inner join tagged on question.id = tagged.question_id;

-- select * from question inner join tagged on question.id = tagged.question_id where tagged.tag = 'katt';

-- delete from question where id = 1;

-- update tag set num=3 where tag = tag;

-- delete from question where id = 1;

-- select count(*) from tagged where tag = tag;

-- insert into question (title, question) values ('apafdasf', 'asfsfasf');
--  
-- insert into tag (tag) values ('apa');
--  
-- insert into tagged (tag, question_id) values ('apa', 3);
-- -- 
-- insert into tag (tag) values ('apa') ON DUPLICATE KEY UPDATE tag='apa';
-- 
-- insert into user (acronym, email, name) values ('kalle','kalle@anka.se', 'Karl');

-- select tag, count(*) as num from tagged group by tag;

-- delete from tagged where tag = 'apa' and question_id = 5; 

-- insert into tag (tag, question_id) values ('apa', 1) ON DUPLICATE KEY UPDATE question_id='1';

-- select * from question order by created desc limit 10;
-- 
-- select * from question;

-- update user set posts = null where id = 1;

-- SHOW ENGINE INNODB STATUS; is used to show really good info is the is a problem. for exmaple what caused the foreiigin key constraint. 

SELECT * FROM comment INNER JOIN user ON user.id = comment.user_id WHERE user.acronym = 'kalle';

SELECT * FROM question INNER JOIN tag ON question.id = tag.question_id WHERE tag.tag = 'apa';

select * from question where deleted is null order by created desc limit 10;
