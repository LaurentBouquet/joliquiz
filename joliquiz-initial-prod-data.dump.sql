--
-- PostgreSQL database dump
--

-- Dumped from database version 10.4 (Ubuntu 10.4-0ubuntu0.18.04)
-- Dumped by pg_dump version 10.4 (Ubuntu 10.4-0ubuntu0.18.04)

SET statement_timeout = 0;
SET lock_timeout = 0;
SET idle_in_transaction_session_timeout = 0;
SET client_encoding = 'UTF8';
SET standard_conforming_strings = on;
SELECT pg_catalog.set_config('search_path', '', false);
SET check_function_bodies = false;
SET client_min_messages = warning;
SET row_security = off;

ALTER TABLE ONLY public.tbl_history_question DROP CONSTRAINT fk_fcd2a776a6cccfc9;
ALTER TABLE ONLY public.tbl_question_category DROP CONSTRAINT fk_db45675f1e27f6bf;
ALTER TABLE ONLY public.tbl_question_category DROP CONSTRAINT fk_db45675f12469de2;
ALTER TABLE ONLY public.tbl_quiz_category DROP CONSTRAINT fk_c91a858f853cd175;
ALTER TABLE ONLY public.tbl_quiz_category DROP CONSTRAINT fk_c91a858f12469de2;
ALTER TABLE ONLY public.tbl_history_answer DROP CONSTRAINT fk_9e994d3b79d11bda;
ALTER TABLE ONLY public.tbl_answer DROP CONSTRAINT fk_577b239a1e27f6bf;
ALTER TABLE ONLY public.tbl_workout DROP CONSTRAINT fk_3fccf306cb944f1a;
ALTER TABLE ONLY public.tbl_workout DROP CONSTRAINT fk_3fccf306853cd175;
DROP INDEX public.uniq_38b383a1f85e0677;
DROP INDEX public.uniq_38b383a1e7927c74;
DROP INDEX public.idx_fcd2a776a6cccfc9;
DROP INDEX public.idx_db45675f1e27f6bf;
DROP INDEX public.idx_db45675f12469de2;
DROP INDEX public.idx_c91a858f853cd175;
DROP INDEX public.idx_c91a858f12469de2;
DROP INDEX public.idx_9e994d3b79d11bda;
DROP INDEX public.idx_577b239a1e27f6bf;
DROP INDEX public.idx_3fccf306cb944f1a;
DROP INDEX public.idx_3fccf306853cd175;
ALTER TABLE ONLY public.tbl_workout DROP CONSTRAINT tbl_workout_pkey;
ALTER TABLE ONLY public.tbl_user DROP CONSTRAINT tbl_user_pkey;
ALTER TABLE ONLY public.tbl_quiz DROP CONSTRAINT tbl_quiz_pkey;
ALTER TABLE ONLY public.tbl_quiz_category DROP CONSTRAINT tbl_quiz_category_pkey;
ALTER TABLE ONLY public.tbl_question DROP CONSTRAINT tbl_question_pkey;
ALTER TABLE ONLY public.tbl_question_category DROP CONSTRAINT tbl_question_category_pkey;
ALTER TABLE ONLY public.tbl_history_question DROP CONSTRAINT tbl_history_question_pkey;
ALTER TABLE ONLY public.tbl_history_answer DROP CONSTRAINT tbl_history_answer_pkey;
ALTER TABLE ONLY public.tbl_category DROP CONSTRAINT tbl_category_pkey;
ALTER TABLE ONLY public.tbl_answer DROP CONSTRAINT tbl_answer_pkey;
ALTER TABLE ONLY public.migration_versions DROP CONSTRAINT migration_versions_pkey;
DROP SEQUENCE public.tbl_workout_id_seq;
DROP TABLE public.tbl_workout;
DROP SEQUENCE public.tbl_user_id_seq;
DROP TABLE public.tbl_user;
DROP SEQUENCE public.tbl_quiz_id_seq;
DROP TABLE public.tbl_quiz_category;
DROP TABLE public.tbl_quiz;
DROP SEQUENCE public.tbl_question_id_seq;
DROP TABLE public.tbl_question_category;
DROP TABLE public.tbl_question;
DROP SEQUENCE public.tbl_history_question_id_seq;
DROP TABLE public.tbl_history_question;
DROP SEQUENCE public.tbl_history_answer_id_seq;
DROP TABLE public.tbl_history_answer;
DROP SEQUENCE public.tbl_category_id_seq;
DROP TABLE public.tbl_category;
DROP SEQUENCE public.tbl_answer_id_seq;
DROP TABLE public.tbl_answer;
DROP TABLE public.migration_versions;
DROP EXTENSION plpgsql;
DROP SCHEMA public;
--
-- Name: public; Type: SCHEMA; Schema: -; Owner: -
--

CREATE SCHEMA public;


--
-- Name: SCHEMA public; Type: COMMENT; Schema: -; Owner: -
--

COMMENT ON SCHEMA public IS 'standard public schema';


--
-- Name: plpgsql; Type: EXTENSION; Schema: -; Owner: -
--

CREATE EXTENSION IF NOT EXISTS plpgsql WITH SCHEMA pg_catalog;


--
-- Name: EXTENSION plpgsql; Type: COMMENT; Schema: -; Owner: -
--

COMMENT ON EXTENSION plpgsql IS 'PL/pgSQL procedural language';


SET default_tablespace = '';

SET default_with_oids = false;

--
-- Name: migration_versions; Type: TABLE; Schema: public; Owner: -
--

CREATE TABLE public.migration_versions (
    version character varying(255) NOT NULL
);


--
-- Name: tbl_answer; Type: TABLE; Schema: public; Owner: -
--

CREATE TABLE public.tbl_answer (
    id integer NOT NULL,
    question_id integer NOT NULL,
    text text NOT NULL,
    correct boolean NOT NULL
);


--
-- Name: tbl_answer_id_seq; Type: SEQUENCE; Schema: public; Owner: -
--

CREATE SEQUENCE public.tbl_answer_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--
-- Name: tbl_category; Type: TABLE; Schema: public; Owner: -
--

CREATE TABLE public.tbl_category (
    id integer NOT NULL,
    shortname character varying(50) NOT NULL,
    longname character varying(255) NOT NULL
);


--
-- Name: tbl_category_id_seq; Type: SEQUENCE; Schema: public; Owner: -
--

CREATE SEQUENCE public.tbl_category_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--
-- Name: tbl_history_answer; Type: TABLE; Schema: public; Owner: -
--

CREATE TABLE public.tbl_history_answer (
    id integer NOT NULL,
    question_history_id integer NOT NULL,
    answer_id integer NOT NULL,
    answer_text text NOT NULL,
    answer_correct boolean NOT NULL,
    correct_given boolean NOT NULL,
    answer_succes boolean NOT NULL
);


--
-- Name: tbl_history_answer_id_seq; Type: SEQUENCE; Schema: public; Owner: -
--

CREATE SEQUENCE public.tbl_history_answer_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--
-- Name: tbl_history_question; Type: TABLE; Schema: public; Owner: -
--

CREATE TABLE public.tbl_history_question (
    id integer NOT NULL,
    workout_id integer NOT NULL,
    question_id integer NOT NULL,
    question_text text NOT NULL,
    completed boolean NOT NULL,
    question_success boolean,
    duration character(255) DEFAULT NULL::bpchar,
    started_at timestamp(0) without time zone NOT NULL,
    ended_at timestamp(0) without time zone DEFAULT NULL::timestamp without time zone
);


--
-- Name: COLUMN tbl_history_question.duration; Type: COMMENT; Schema: public; Owner: -
--

COMMENT ON COLUMN public.tbl_history_question.duration IS '(DC2Type:dateinterval)';


--
-- Name: tbl_history_question_id_seq; Type: SEQUENCE; Schema: public; Owner: -
--

CREATE SEQUENCE public.tbl_history_question_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--
-- Name: tbl_question; Type: TABLE; Schema: public; Owner: -
--

CREATE TABLE public.tbl_question (
    id integer NOT NULL,
    text text NOT NULL,
    created_at timestamp(0) without time zone NOT NULL,
    updated_at timestamp(0) without time zone NOT NULL
);


--
-- Name: tbl_question_category; Type: TABLE; Schema: public; Owner: -
--

CREATE TABLE public.tbl_question_category (
    question_id integer NOT NULL,
    category_id integer NOT NULL
);


--
-- Name: tbl_question_id_seq; Type: SEQUENCE; Schema: public; Owner: -
--

CREATE SEQUENCE public.tbl_question_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--
-- Name: tbl_quiz; Type: TABLE; Schema: public; Owner: -
--

CREATE TABLE public.tbl_quiz (
    id integer NOT NULL,
    title character varying(255) NOT NULL,
    summary text,
    number_of_questions integer NOT NULL,
    active boolean NOT NULL,
    created_at timestamp(0) without time zone NOT NULL,
    updated_at timestamp(0) without time zone NOT NULL,
    show_result_question boolean NOT NULL,
    show_result_quiz boolean NOT NULL
);


--
-- Name: tbl_quiz_category; Type: TABLE; Schema: public; Owner: -
--

CREATE TABLE public.tbl_quiz_category (
    quiz_id integer NOT NULL,
    category_id integer NOT NULL
);


--
-- Name: tbl_quiz_id_seq; Type: SEQUENCE; Schema: public; Owner: -
--

CREATE SEQUENCE public.tbl_quiz_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--
-- Name: tbl_user; Type: TABLE; Schema: public; Owner: -
--

CREATE TABLE public.tbl_user (
    id integer NOT NULL,
    username character varying(255) NOT NULL,
    email character varying(255) NOT NULL,
    password character varying(64) NOT NULL,
    is_active boolean NOT NULL,
    roles text NOT NULL
);


--
-- Name: COLUMN tbl_user.roles; Type: COMMENT; Schema: public; Owner: -
--

COMMENT ON COLUMN public.tbl_user.roles IS '(DC2Type:array)';


--
-- Name: tbl_user_id_seq; Type: SEQUENCE; Schema: public; Owner: -
--

CREATE SEQUENCE public.tbl_user_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--
-- Name: tbl_workout; Type: TABLE; Schema: public; Owner: -
--

CREATE TABLE public.tbl_workout (
    id integer NOT NULL,
    student_id integer NOT NULL,
    quiz_id integer NOT NULL,
    started_at timestamp(0) without time zone NOT NULL,
    ended_at timestamp(0) without time zone DEFAULT NULL::timestamp without time zone,
    number_of_questions integer NOT NULL,
    completed boolean NOT NULL
);


--
-- Name: tbl_workout_id_seq; Type: SEQUENCE; Schema: public; Owner: -
--

CREATE SEQUENCE public.tbl_workout_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--
-- Data for Name: migration_versions; Type: TABLE DATA; Schema: public; Owner: -
--

COPY public.migration_versions (version) FROM stdin;
20180707214807
\.


--
-- Data for Name: tbl_answer; Type: TABLE DATA; Schema: public; Owner: -
--

COPY public.tbl_answer (id, question_id, text, correct) FROM stdin;
1	1	<!	f
2	1	<%	f
3	1	<?	t
4	1	<?=	f
5	2	!	f
6	2	**	f
7	2	<	t
8	2	and	f
9	3	use function foo;	f
10	3	use Myapp\\Utils\\Bar\\foo;	f
11	3	use function Myapp\\Utils\\Bar\\foo;	t
12	3	use Utils\\Bar\\Foo;	f
13	4	True	f
14	4	False	t
15	5	SOAP and XML-RPC	t
16	5	REST and SOAP	f
17	5	Corba and XML-RPC	f
18	5	XML-RPC and REST	f
19	6	case insensitive and case sensitive	f
20	6	case sensitive and case sensitive	f
21	6	case sensitive and case insensitive	t
22	6	case insensitive and case insensitive	f
23	7	Yes, in all cases	f
24	7	No, in all cases	t
25	7	Yes, but it depends on the function scope	f
26	7	Yes, except when it is an anonymous function	f
27	8	In PHP>=5.6 argument lists may include the ... token to denote that the function accepts a variable number of arguments	f
28	8	A function name, as other labels in PHP, must match the following regular expression: [a-zA-Z_-ÿ][a-zA-Z0-9_-ÿ]*	f
29	8	Variable functions work with language constructs such as echo, print, unset(), isset(), empty(), include or require	t
30	8	Assigning a closure (anonymous function) to a variable uses the same syntax as any other assignment, including the trailing semicolon	f
31	9	function &foo() {...}	t
32	9	function $foo() {...}	f
33	9	function %foo() {...}	f
34	9	function $$foo() {...}	f
35	10	abstract	t
36	10	final	f
37	10	protected	f
38	10	incomplete	f
39	10	implements	f
40	11	PSR-0	f
41	11	PSR-1	t
42	11	PSR-2	t
43	11	PSR-3	f
44	11	PSR-4	f
45	12	Traits	t
46	12	Object Cloning	f
47	12	ReflectionClass	f
48	12	Inheritance	f
49	13	True	t
50	13	False	f
51	14	True	f
52	14	False	t
\.


--
-- Data for Name: tbl_category; Type: TABLE DATA; Schema: public; Owner: -
--

COPY public.tbl_category (id, shortname, longname) FROM stdin;
1	Symfony	Symfony (all versions)
2	Symfony3	Symfony version 3
3	Symfony4	Symfony version 4
4	PHP	PHP
\.


--
-- Data for Name: tbl_history_answer; Type: TABLE DATA; Schema: public; Owner: -
--

COPY public.tbl_history_answer (id, question_history_id, answer_id, answer_text, answer_correct, correct_given, answer_succes) FROM stdin;
1	1	51	True	f	t	f
2	1	52	False	t	f	f
3	1	51	True	f	f	t
4	1	52	False	t	f	f
5	2	13	True	f	f	t
6	2	14	False	t	t	t
\.


--
-- Data for Name: tbl_history_question; Type: TABLE DATA; Schema: public; Owner: -
--

COPY public.tbl_history_question (id, workout_id, question_id, question_text, completed, question_success, duration, started_at, ended_at) FROM stdin;
1	1	14	True or False ? A lambda function is a named PHP function that can be stored in a variable.	f	f	-P00Y00M00DT00H00M09S                                                                                                                                                                                                                                          	2018-07-09 19:34:07	2018-07-09 19:34:16
2	1	4	True or False? It is possible to import all classes from a namespace in PHP.	f	t	-P00Y00M00DT00H00M06S                                                                                                                                                                                                                                          	2018-07-09 19:34:18	2018-07-09 19:34:24
\.


--
-- Data for Name: tbl_question; Type: TABLE DATA; Schema: public; Owner: -
--

COPY public.tbl_question (id, text, created_at, updated_at) FROM stdin;
1	What is the short open tag for PHP?	2018-07-09 19:33:33	2018-07-09 19:33:33
2	Which of these operators is non-associative?	2018-07-09 19:33:33	2018-07-09 19:33:33
3	Since PHP 5.6+, if function foo() is defined in the namespace Myapp\\Utils\\Bar and your code is in namespace Myapp, what is the correct way to import the foo() function?	2018-07-09 19:33:33	2018-07-09 19:33:33
4	True or False? It is possible to import all classes from a namespace in PHP.	2018-07-09 19:33:33	2018-07-09 19:33:33
5	Which web services are supported natively in PHP?	2018-07-09 19:33:33	2018-07-09 19:33:33
6	Variable names and function names are, respectively:	2018-07-09 19:33:33	2018-07-09 19:33:33
7	Does PHP support function overloading?	2018-07-09 19:33:33	2018-07-09 19:33:33
8	Which of the following statements is not true?	2018-07-09 19:33:33	2018-07-09 19:33:33
9	Which of the following function declarations must be used to return a reference?	2018-07-09 19:33:33	2018-07-09 19:33:33
10	The ______ keyword is used to indicate an incomplete class or method, which must be further extended and/or implemented in order to be used.	2018-07-09 19:33:33	2018-07-09 19:33:33
11	According to the PHP Framework Interoperability Group, which PSRs concern best coding practices ?	2018-07-09 19:33:33	2018-07-09 19:33:33
12	Since PHP 5.4, which functionality allows horizontal composition of behavior ?	2018-07-09 19:33:33	2018-07-09 19:33:33
13	True or False ? A closure is a lambda function that is aware of its surrounding context.	2018-07-09 19:33:33	2018-07-09 19:33:33
14	True or False ? A lambda function is a named PHP function that can be stored in a variable.	2018-07-09 19:33:33	2018-07-09 19:33:33
\.


--
-- Data for Name: tbl_question_category; Type: TABLE DATA; Schema: public; Owner: -
--

COPY public.tbl_question_category (question_id, category_id) FROM stdin;
1	4
2	4
3	4
4	4
5	4
6	4
7	4
8	4
9	4
10	4
11	4
12	4
13	4
14	4
\.


--
-- Data for Name: tbl_quiz; Type: TABLE DATA; Schema: public; Owner: -
--

COPY public.tbl_quiz (id, title, summary, number_of_questions, active, created_at, updated_at, show_result_question, show_result_quiz) FROM stdin;
1	PHP	\N	10	t	2018-07-09 19:34:02	2018-07-09 19:34:02	t	f
\.


--
-- Data for Name: tbl_quiz_category; Type: TABLE DATA; Schema: public; Owner: -
--

COPY public.tbl_quiz_category (quiz_id, category_id) FROM stdin;
1	4
\.


--
-- Data for Name: tbl_user; Type: TABLE DATA; Schema: public; Owner: -
--

COPY public.tbl_user (id, username, email, password, is_active, roles) FROM stdin;
1	superadmin	superadmin@domain.tld	$2y$13$IafE/mRTijixrNYc56559OCBClJzPV20wlQNfIqNxkzKPT/vQdmSO	t	a:2:{i:0;s:16:"ROLE_SUPER_ADMIN";i:1;s:9:"ROLE_USER";}
2	admin	admin@domain.tld	$2y$13$O3J64Ao7vQVqm8jxkwqGZOECTKw3F5YIWb2uG2/3ypHiJO.BQlWVa	t	a:2:{i:0;s:10:"ROLE_ADMIN";i:1;s:9:"ROLE_USER";}
3	teacher	teacher@domain.tld	$2y$13$i6VTZtz4JXswavrkYH9SzeEk9duEKGqEdsSUs7bJZ5VVu5yeJu5QS	t	a:2:{i:0;s:12:"ROLE_TEACHER";i:1;s:9:"ROLE_USER";}
4	user	user@domain.tld	$2y$13$iVfTWcCfMKTKzZ92VHM7ReEWkewM66d/d1GRGECIMHjzUdLVhrTTO	t	a:1:{i:0;s:9:"ROLE_USER";}
\.


--
-- Data for Name: tbl_workout; Type: TABLE DATA; Schema: public; Owner: -
--

COPY public.tbl_workout (id, student_id, quiz_id, started_at, ended_at, number_of_questions, completed) FROM stdin;
1	1	1	2018-07-09 19:34:05	2018-07-09 19:34:24	2	f
\.


--
-- Name: tbl_answer_id_seq; Type: SEQUENCE SET; Schema: public; Owner: -
--

SELECT pg_catalog.setval('public.tbl_answer_id_seq', 52, true);


--
-- Name: tbl_category_id_seq; Type: SEQUENCE SET; Schema: public; Owner: -
--

SELECT pg_catalog.setval('public.tbl_category_id_seq', 4, true);


--
-- Name: tbl_history_answer_id_seq; Type: SEQUENCE SET; Schema: public; Owner: -
--

SELECT pg_catalog.setval('public.tbl_history_answer_id_seq', 6, true);


--
-- Name: tbl_history_question_id_seq; Type: SEQUENCE SET; Schema: public; Owner: -
--

SELECT pg_catalog.setval('public.tbl_history_question_id_seq', 2, true);


--
-- Name: tbl_question_id_seq; Type: SEQUENCE SET; Schema: public; Owner: -
--

SELECT pg_catalog.setval('public.tbl_question_id_seq', 14, true);


--
-- Name: tbl_quiz_id_seq; Type: SEQUENCE SET; Schema: public; Owner: -
--

SELECT pg_catalog.setval('public.tbl_quiz_id_seq', 1, true);


--
-- Name: tbl_user_id_seq; Type: SEQUENCE SET; Schema: public; Owner: -
--

SELECT pg_catalog.setval('public.tbl_user_id_seq', 4, true);


--
-- Name: tbl_workout_id_seq; Type: SEQUENCE SET; Schema: public; Owner: -
--

SELECT pg_catalog.setval('public.tbl_workout_id_seq', 1, true);


--
-- Name: migration_versions migration_versions_pkey; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.migration_versions
    ADD CONSTRAINT migration_versions_pkey PRIMARY KEY (version);


--
-- Name: tbl_answer tbl_answer_pkey; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.tbl_answer
    ADD CONSTRAINT tbl_answer_pkey PRIMARY KEY (id);


--
-- Name: tbl_category tbl_category_pkey; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.tbl_category
    ADD CONSTRAINT tbl_category_pkey PRIMARY KEY (id);


--
-- Name: tbl_history_answer tbl_history_answer_pkey; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.tbl_history_answer
    ADD CONSTRAINT tbl_history_answer_pkey PRIMARY KEY (id);


--
-- Name: tbl_history_question tbl_history_question_pkey; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.tbl_history_question
    ADD CONSTRAINT tbl_history_question_pkey PRIMARY KEY (id);


--
-- Name: tbl_question_category tbl_question_category_pkey; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.tbl_question_category
    ADD CONSTRAINT tbl_question_category_pkey PRIMARY KEY (question_id, category_id);


--
-- Name: tbl_question tbl_question_pkey; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.tbl_question
    ADD CONSTRAINT tbl_question_pkey PRIMARY KEY (id);


--
-- Name: tbl_quiz_category tbl_quiz_category_pkey; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.tbl_quiz_category
    ADD CONSTRAINT tbl_quiz_category_pkey PRIMARY KEY (quiz_id, category_id);


--
-- Name: tbl_quiz tbl_quiz_pkey; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.tbl_quiz
    ADD CONSTRAINT tbl_quiz_pkey PRIMARY KEY (id);


--
-- Name: tbl_user tbl_user_pkey; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.tbl_user
    ADD CONSTRAINT tbl_user_pkey PRIMARY KEY (id);


--
-- Name: tbl_workout tbl_workout_pkey; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.tbl_workout
    ADD CONSTRAINT tbl_workout_pkey PRIMARY KEY (id);


--
-- Name: idx_3fccf306853cd175; Type: INDEX; Schema: public; Owner: -
--

CREATE INDEX idx_3fccf306853cd175 ON public.tbl_workout USING btree (quiz_id);


--
-- Name: idx_3fccf306cb944f1a; Type: INDEX; Schema: public; Owner: -
--

CREATE INDEX idx_3fccf306cb944f1a ON public.tbl_workout USING btree (student_id);


--
-- Name: idx_577b239a1e27f6bf; Type: INDEX; Schema: public; Owner: -
--

CREATE INDEX idx_577b239a1e27f6bf ON public.tbl_answer USING btree (question_id);


--
-- Name: idx_9e994d3b79d11bda; Type: INDEX; Schema: public; Owner: -
--

CREATE INDEX idx_9e994d3b79d11bda ON public.tbl_history_answer USING btree (question_history_id);


--
-- Name: idx_c91a858f12469de2; Type: INDEX; Schema: public; Owner: -
--

CREATE INDEX idx_c91a858f12469de2 ON public.tbl_quiz_category USING btree (category_id);


--
-- Name: idx_c91a858f853cd175; Type: INDEX; Schema: public; Owner: -
--

CREATE INDEX idx_c91a858f853cd175 ON public.tbl_quiz_category USING btree (quiz_id);


--
-- Name: idx_db45675f12469de2; Type: INDEX; Schema: public; Owner: -
--

CREATE INDEX idx_db45675f12469de2 ON public.tbl_question_category USING btree (category_id);


--
-- Name: idx_db45675f1e27f6bf; Type: INDEX; Schema: public; Owner: -
--

CREATE INDEX idx_db45675f1e27f6bf ON public.tbl_question_category USING btree (question_id);


--
-- Name: idx_fcd2a776a6cccfc9; Type: INDEX; Schema: public; Owner: -
--

CREATE INDEX idx_fcd2a776a6cccfc9 ON public.tbl_history_question USING btree (workout_id);


--
-- Name: uniq_38b383a1e7927c74; Type: INDEX; Schema: public; Owner: -
--

CREATE UNIQUE INDEX uniq_38b383a1e7927c74 ON public.tbl_user USING btree (email);


--
-- Name: uniq_38b383a1f85e0677; Type: INDEX; Schema: public; Owner: -
--

CREATE UNIQUE INDEX uniq_38b383a1f85e0677 ON public.tbl_user USING btree (username);


--
-- Name: tbl_workout fk_3fccf306853cd175; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.tbl_workout
    ADD CONSTRAINT fk_3fccf306853cd175 FOREIGN KEY (quiz_id) REFERENCES public.tbl_quiz(id);


--
-- Name: tbl_workout fk_3fccf306cb944f1a; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.tbl_workout
    ADD CONSTRAINT fk_3fccf306cb944f1a FOREIGN KEY (student_id) REFERENCES public.tbl_user(id);


--
-- Name: tbl_answer fk_577b239a1e27f6bf; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.tbl_answer
    ADD CONSTRAINT fk_577b239a1e27f6bf FOREIGN KEY (question_id) REFERENCES public.tbl_question(id);


--
-- Name: tbl_history_answer fk_9e994d3b79d11bda; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.tbl_history_answer
    ADD CONSTRAINT fk_9e994d3b79d11bda FOREIGN KEY (question_history_id) REFERENCES public.tbl_history_question(id);


--
-- Name: tbl_quiz_category fk_c91a858f12469de2; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.tbl_quiz_category
    ADD CONSTRAINT fk_c91a858f12469de2 FOREIGN KEY (category_id) REFERENCES public.tbl_category(id) ON DELETE CASCADE;


--
-- Name: tbl_quiz_category fk_c91a858f853cd175; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.tbl_quiz_category
    ADD CONSTRAINT fk_c91a858f853cd175 FOREIGN KEY (quiz_id) REFERENCES public.tbl_quiz(id) ON DELETE CASCADE;


--
-- Name: tbl_question_category fk_db45675f12469de2; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.tbl_question_category
    ADD CONSTRAINT fk_db45675f12469de2 FOREIGN KEY (category_id) REFERENCES public.tbl_category(id) ON DELETE CASCADE;


--
-- Name: tbl_question_category fk_db45675f1e27f6bf; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.tbl_question_category
    ADD CONSTRAINT fk_db45675f1e27f6bf FOREIGN KEY (question_id) REFERENCES public.tbl_question(id) ON DELETE CASCADE;


--
-- Name: tbl_history_question fk_fcd2a776a6cccfc9; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.tbl_history_question
    ADD CONSTRAINT fk_fcd2a776a6cccfc9 FOREIGN KEY (workout_id) REFERENCES public.tbl_workout(id);


--
-- PostgreSQL database dump complete
--

