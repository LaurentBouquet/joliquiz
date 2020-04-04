--
-- PostgreSQL database dump
--

-- Dumped from database version 10.4 (Ubuntu 10.4-0ubuntu0.18.04)
-- Dumped by pg_dump version 10.4 (Ubuntu 10.4-0ubuntu0.18.04)

-- Started on 2018-07-17 15:41:32 CEST

SET statement_timeout = 0;
SET lock_timeout = 0;
SET idle_in_transaction_session_timeout = 0;
SET client_encoding = 'UTF8';
SET standard_conforming_strings = on;
SELECT pg_catalog.set_config('search_path', '', false);
SET check_function_bodies = false;
SET client_min_messages = warning;
SET row_security = off;

--
-- TOC entry 1 (class 3079 OID 13049)
-- Name: plpgsql; Type: EXTENSION; Schema: -; Owner: 
--

CREATE EXTENSION IF NOT EXISTS plpgsql WITH SCHEMA pg_catalog;


--
-- TOC entry 3063 (class 0 OID 0)
-- Dependencies: 1
-- Name: EXTENSION plpgsql; Type: COMMENT; Schema: -; Owner: 
--

COMMENT ON EXTENSION plpgsql IS 'PL/pgSQL procedural language';


SET default_tablespace = '';

SET default_with_oids = false;

--
-- TOC entry 196 (class 1259 OID 29544)
-- Name: migration_versions; Type: TABLE; Schema: public; Owner: joliquiz
--

CREATE TABLE public.migration_versions (
    version character varying(255) NOT NULL
);


ALTER TABLE public.migration_versions OWNER TO joliquiz;

--
-- TOC entry 205 (class 1259 OID 29565)
-- Name: tbl_answer; Type: TABLE; Schema: public; Owner: joliquiz
--

CREATE TABLE public.tbl_answer (
    id integer NOT NULL,
    question_id integer NOT NULL,
    text text NOT NULL,
    correct boolean NOT NULL
);


ALTER TABLE public.tbl_answer OWNER TO joliquiz;

--
-- TOC entry 197 (class 1259 OID 29549)
-- Name: tbl_answer_id_seq; Type: SEQUENCE; Schema: public; Owner: joliquiz
--

CREATE SEQUENCE public.tbl_answer_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE public.tbl_answer_id_seq OWNER TO joliquiz;

--
-- TOC entry 206 (class 1259 OID 29574)
-- Name: tbl_category; Type: TABLE; Schema: public; Owner: joliquiz
--

CREATE TABLE public.tbl_category (
    id integer NOT NULL,
    shortname character varying(50) NOT NULL,
    longname character varying(255) NOT NULL,
    language_id character varying(2) NOT NULL
);


ALTER TABLE public.tbl_category OWNER TO joliquiz;

--
-- TOC entry 198 (class 1259 OID 29551)
-- Name: tbl_category_id_seq; Type: SEQUENCE; Schema: public; Owner: joliquiz
--

CREATE SEQUENCE public.tbl_category_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE public.tbl_category_id_seq OWNER TO joliquiz;

--
-- TOC entry 214 (class 1259 OID 29638)
-- Name: tbl_history_answer; Type: TABLE; Schema: public; Owner: joliquiz
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


ALTER TABLE public.tbl_history_answer OWNER TO joliquiz;

--
-- TOC entry 204 (class 1259 OID 29563)
-- Name: tbl_history_answer_id_seq; Type: SEQUENCE; Schema: public; Owner: joliquiz
--

CREATE SEQUENCE public.tbl_history_answer_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE public.tbl_history_answer_id_seq OWNER TO joliquiz;

--
-- TOC entry 213 (class 1259 OID 29627)
-- Name: tbl_history_question; Type: TABLE; Schema: public; Owner: joliquiz
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


ALTER TABLE public.tbl_history_question OWNER TO joliquiz;

--
-- TOC entry 3064 (class 0 OID 0)
-- Dependencies: 213
-- Name: COLUMN tbl_history_question.duration; Type: COMMENT; Schema: public; Owner: joliquiz
--

COMMENT ON COLUMN public.tbl_history_question.duration IS '(DC2Type:dateinterval)';


--
-- TOC entry 203 (class 1259 OID 29561)
-- Name: tbl_history_question_id_seq; Type: SEQUENCE; Schema: public; Owner: joliquiz
--

CREATE SEQUENCE public.tbl_history_question_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE public.tbl_history_question_id_seq OWNER TO joliquiz;

--
-- TOC entry 215 (class 1259 OID 29700)
-- Name: tbl_language; Type: TABLE; Schema: public; Owner: joliquiz
--

CREATE TABLE public.tbl_language (
    id character varying(2) NOT NULL,
    english_name character varying(50) NOT NULL,
    native_name character varying(50) NOT NULL
);


ALTER TABLE public.tbl_language OWNER TO joliquiz;

--
-- TOC entry 207 (class 1259 OID 29579)
-- Name: tbl_question; Type: TABLE; Schema: public; Owner: joliquiz
--

CREATE TABLE public.tbl_question (
    id integer NOT NULL,
    text text NOT NULL,
    created_at timestamp(0) without time zone NOT NULL,
    updated_at timestamp(0) without time zone NOT NULL,
    language_id character varying(2) NOT NULL
);


ALTER TABLE public.tbl_question OWNER TO joliquiz;

--
-- TOC entry 208 (class 1259 OID 29587)
-- Name: tbl_question_category; Type: TABLE; Schema: public; Owner: joliquiz
--

CREATE TABLE public.tbl_question_category (
    question_id integer NOT NULL,
    category_id integer NOT NULL
);


ALTER TABLE public.tbl_question_category OWNER TO joliquiz;

--
-- TOC entry 199 (class 1259 OID 29553)
-- Name: tbl_question_id_seq; Type: SEQUENCE; Schema: public; Owner: joliquiz
--

CREATE SEQUENCE public.tbl_question_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE public.tbl_question_id_seq OWNER TO joliquiz;

--
-- TOC entry 210 (class 1259 OID 29602)
-- Name: tbl_quiz; Type: TABLE; Schema: public; Owner: joliquiz
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
    show_result_quiz boolean NOT NULL,
    language_id character varying(2) NOT NULL
);


ALTER TABLE public.tbl_quiz OWNER TO joliquiz;

--
-- TOC entry 211 (class 1259 OID 29610)
-- Name: tbl_quiz_category; Type: TABLE; Schema: public; Owner: joliquiz
--

CREATE TABLE public.tbl_quiz_category (
    quiz_id integer NOT NULL,
    category_id integer NOT NULL
);


ALTER TABLE public.tbl_quiz_category OWNER TO joliquiz;

--
-- TOC entry 201 (class 1259 OID 29557)
-- Name: tbl_quiz_id_seq; Type: SEQUENCE; Schema: public; Owner: joliquiz
--

CREATE SEQUENCE public.tbl_quiz_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE public.tbl_quiz_id_seq OWNER TO joliquiz;

--
-- TOC entry 212 (class 1259 OID 29617)
-- Name: tbl_user; Type: TABLE; Schema: public; Owner: joliquiz
--

CREATE TABLE public.tbl_user (
    id integer NOT NULL,
    username character varying(255) NOT NULL,
    email character varying(255) NOT NULL,
    password character varying(64) NOT NULL,
    is_active boolean NOT NULL,
    roles text NOT NULL,
    prefered_language_id character varying(2) DEFAULT NULL::character varying
);


ALTER TABLE public.tbl_user OWNER TO joliquiz;

--
-- TOC entry 3065 (class 0 OID 0)
-- Dependencies: 212
-- Name: COLUMN tbl_user.roles; Type: COMMENT; Schema: public; Owner: joliquiz
--

COMMENT ON COLUMN public.tbl_user.roles IS '(DC2Type:array)';


--
-- TOC entry 202 (class 1259 OID 29559)
-- Name: tbl_user_id_seq; Type: SEQUENCE; Schema: public; Owner: joliquiz
--

CREATE SEQUENCE public.tbl_user_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE public.tbl_user_id_seq OWNER TO joliquiz;

--
-- TOC entry 209 (class 1259 OID 29594)
-- Name: tbl_workout; Type: TABLE; Schema: public; Owner: joliquiz
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


ALTER TABLE public.tbl_workout OWNER TO joliquiz;

--
-- TOC entry 200 (class 1259 OID 29555)
-- Name: tbl_workout_id_seq; Type: SEQUENCE; Schema: public; Owner: joliquiz
--

CREATE SEQUENCE public.tbl_workout_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE public.tbl_workout_id_seq OWNER TO joliquiz;

--
-- TOC entry 3036 (class 0 OID 29544)
-- Dependencies: 196
-- Data for Name: migration_versions; Type: TABLE DATA; Schema: public; Owner: joliquiz
--

COPY public.migration_versions (version) FROM stdin;
20180707214807
20180716160642
20180717124118
\.


--
-- TOC entry 3045 (class 0 OID 29565)
-- Dependencies: 205
-- Data for Name: tbl_answer; Type: TABLE DATA; Schema: public; Owner: joliquiz
--

COPY public.tbl_answer (id, question_id, text, correct) FROM stdin;
\.


--
-- TOC entry 3046 (class 0 OID 29574)
-- Dependencies: 206
-- Data for Name: tbl_category; Type: TABLE DATA; Schema: public; Owner: joliquiz
--

COPY public.tbl_category (id, shortname, longname, language_id) FROM stdin;
\.


--
-- TOC entry 3054 (class 0 OID 29638)
-- Dependencies: 214
-- Data for Name: tbl_history_answer; Type: TABLE DATA; Schema: public; Owner: joliquiz
--

COPY public.tbl_history_answer (id, question_history_id, answer_id, answer_text, answer_correct, correct_given, answer_succes) FROM stdin;
\.


--
-- TOC entry 3053 (class 0 OID 29627)
-- Dependencies: 213
-- Data for Name: tbl_history_question; Type: TABLE DATA; Schema: public; Owner: joliquiz
--

COPY public.tbl_history_question (id, workout_id, question_id, question_text, completed, question_success, duration, started_at, ended_at) FROM stdin;
\.


--
-- TOC entry 3055 (class 0 OID 29700)
-- Dependencies: 215
-- Data for Name: tbl_language; Type: TABLE DATA; Schema: public; Owner: joliquiz
--

COPY public.tbl_language (id, english_name, native_name) FROM stdin;
\.


--
-- TOC entry 3047 (class 0 OID 29579)
-- Dependencies: 207
-- Data for Name: tbl_question; Type: TABLE DATA; Schema: public; Owner: joliquiz
--

COPY public.tbl_question (id, text, created_at, updated_at, language_id) FROM stdin;
\.


--
-- TOC entry 3048 (class 0 OID 29587)
-- Dependencies: 208
-- Data for Name: tbl_question_category; Type: TABLE DATA; Schema: public; Owner: joliquiz
--

COPY public.tbl_question_category (question_id, category_id) FROM stdin;
\.


--
-- TOC entry 3050 (class 0 OID 29602)
-- Dependencies: 210
-- Data for Name: tbl_quiz; Type: TABLE DATA; Schema: public; Owner: joliquiz
--

COPY public.tbl_quiz (id, title, summary, number_of_questions, active, created_at, updated_at, show_result_question, show_result_quiz, language_id) FROM stdin;
\.


--
-- TOC entry 3051 (class 0 OID 29610)
-- Dependencies: 211
-- Data for Name: tbl_quiz_category; Type: TABLE DATA; Schema: public; Owner: joliquiz
--

COPY public.tbl_quiz_category (quiz_id, category_id) FROM stdin;
\.


--
-- TOC entry 3052 (class 0 OID 29617)
-- Dependencies: 212
-- Data for Name: tbl_user; Type: TABLE DATA; Schema: public; Owner: joliquiz
--

COPY public.tbl_user (id, username, email, password, is_active, roles, prefered_language_id) FROM stdin;
\.


--
-- TOC entry 3049 (class 0 OID 29594)
-- Dependencies: 209
-- Data for Name: tbl_workout; Type: TABLE DATA; Schema: public; Owner: joliquiz
--

COPY public.tbl_workout (id, student_id, quiz_id, started_at, ended_at, number_of_questions, completed) FROM stdin;
\.


--
-- TOC entry 3066 (class 0 OID 0)
-- Dependencies: 197
-- Name: tbl_answer_id_seq; Type: SEQUENCE SET; Schema: public; Owner: joliquiz
--

SELECT pg_catalog.setval('public.tbl_answer_id_seq', 1, false);


--
-- TOC entry 3067 (class 0 OID 0)
-- Dependencies: 198
-- Name: tbl_category_id_seq; Type: SEQUENCE SET; Schema: public; Owner: joliquiz
--

SELECT pg_catalog.setval('public.tbl_category_id_seq', 1, false);


--
-- TOC entry 3068 (class 0 OID 0)
-- Dependencies: 204
-- Name: tbl_history_answer_id_seq; Type: SEQUENCE SET; Schema: public; Owner: joliquiz
--

SELECT pg_catalog.setval('public.tbl_history_answer_id_seq', 1, false);


--
-- TOC entry 3069 (class 0 OID 0)
-- Dependencies: 203
-- Name: tbl_history_question_id_seq; Type: SEQUENCE SET; Schema: public; Owner: joliquiz
--

SELECT pg_catalog.setval('public.tbl_history_question_id_seq', 1, false);


--
-- TOC entry 3070 (class 0 OID 0)
-- Dependencies: 199
-- Name: tbl_question_id_seq; Type: SEQUENCE SET; Schema: public; Owner: joliquiz
--

SELECT pg_catalog.setval('public.tbl_question_id_seq', 1, false);


--
-- TOC entry 3071 (class 0 OID 0)
-- Dependencies: 201
-- Name: tbl_quiz_id_seq; Type: SEQUENCE SET; Schema: public; Owner: joliquiz
--

SELECT pg_catalog.setval('public.tbl_quiz_id_seq', 1, false);


--
-- TOC entry 3072 (class 0 OID 0)
-- Dependencies: 202
-- Name: tbl_user_id_seq; Type: SEQUENCE SET; Schema: public; Owner: joliquiz
--

SELECT pg_catalog.setval('public.tbl_user_id_seq', 1, false);


--
-- TOC entry 3073 (class 0 OID 0)
-- Dependencies: 200
-- Name: tbl_workout_id_seq; Type: SEQUENCE SET; Schema: public; Owner: joliquiz
--

SELECT pg_catalog.setval('public.tbl_workout_id_seq', 1, false);


--
-- TOC entry 2863 (class 2606 OID 29548)
-- Name: migration_versions migration_versions_pkey; Type: CONSTRAINT; Schema: public; Owner: joliquiz
--

ALTER TABLE ONLY public.migration_versions
    ADD CONSTRAINT migration_versions_pkey PRIMARY KEY (version);


--
-- TOC entry 2866 (class 2606 OID 29572)
-- Name: tbl_answer tbl_answer_pkey; Type: CONSTRAINT; Schema: public; Owner: joliquiz
--

ALTER TABLE ONLY public.tbl_answer
    ADD CONSTRAINT tbl_answer_pkey PRIMARY KEY (id);


--
-- TOC entry 2869 (class 2606 OID 29578)
-- Name: tbl_category tbl_category_pkey; Type: CONSTRAINT; Schema: public; Owner: joliquiz
--

ALTER TABLE ONLY public.tbl_category
    ADD CONSTRAINT tbl_category_pkey PRIMARY KEY (id);


--
-- TOC entry 2898 (class 2606 OID 29645)
-- Name: tbl_history_answer tbl_history_answer_pkey; Type: CONSTRAINT; Schema: public; Owner: joliquiz
--

ALTER TABLE ONLY public.tbl_history_answer
    ADD CONSTRAINT tbl_history_answer_pkey PRIMARY KEY (id);


--
-- TOC entry 2895 (class 2606 OID 29636)
-- Name: tbl_history_question tbl_history_question_pkey; Type: CONSTRAINT; Schema: public; Owner: joliquiz
--

ALTER TABLE ONLY public.tbl_history_question
    ADD CONSTRAINT tbl_history_question_pkey PRIMARY KEY (id);


--
-- TOC entry 2900 (class 2606 OID 29704)
-- Name: tbl_language tbl_language_pkey; Type: CONSTRAINT; Schema: public; Owner: joliquiz
--

ALTER TABLE ONLY public.tbl_language
    ADD CONSTRAINT tbl_language_pkey PRIMARY KEY (id);


--
-- TOC entry 2876 (class 2606 OID 29591)
-- Name: tbl_question_category tbl_question_category_pkey; Type: CONSTRAINT; Schema: public; Owner: joliquiz
--

ALTER TABLE ONLY public.tbl_question_category
    ADD CONSTRAINT tbl_question_category_pkey PRIMARY KEY (question_id, category_id);


--
-- TOC entry 2872 (class 2606 OID 29586)
-- Name: tbl_question tbl_question_pkey; Type: CONSTRAINT; Schema: public; Owner: joliquiz
--

ALTER TABLE ONLY public.tbl_question
    ADD CONSTRAINT tbl_question_pkey PRIMARY KEY (id);


--
-- TOC entry 2887 (class 2606 OID 29614)
-- Name: tbl_quiz_category tbl_quiz_category_pkey; Type: CONSTRAINT; Schema: public; Owner: joliquiz
--

ALTER TABLE ONLY public.tbl_quiz_category
    ADD CONSTRAINT tbl_quiz_category_pkey PRIMARY KEY (quiz_id, category_id);


--
-- TOC entry 2883 (class 2606 OID 29609)
-- Name: tbl_quiz tbl_quiz_pkey; Type: CONSTRAINT; Schema: public; Owner: joliquiz
--

ALTER TABLE ONLY public.tbl_quiz
    ADD CONSTRAINT tbl_quiz_pkey PRIMARY KEY (id);


--
-- TOC entry 2890 (class 2606 OID 29624)
-- Name: tbl_user tbl_user_pkey; Type: CONSTRAINT; Schema: public; Owner: joliquiz
--

ALTER TABLE ONLY public.tbl_user
    ADD CONSTRAINT tbl_user_pkey PRIMARY KEY (id);


--
-- TOC entry 2880 (class 2606 OID 29599)
-- Name: tbl_workout tbl_workout_pkey; Type: CONSTRAINT; Schema: public; Owner: joliquiz
--

ALTER TABLE ONLY public.tbl_workout
    ADD CONSTRAINT tbl_workout_pkey PRIMARY KEY (id);


--
-- TOC entry 2881 (class 1259 OID 29733)
-- Name: idx_1132af7a82f1baf4; Type: INDEX; Schema: public; Owner: joliquiz
--

CREATE INDEX idx_1132af7a82f1baf4 ON public.tbl_quiz USING btree (language_id);


--
-- TOC entry 2888 (class 1259 OID 29721)
-- Name: idx_38b383a197e28a86; Type: INDEX; Schema: public; Owner: joliquiz
--

CREATE INDEX idx_38b383a197e28a86 ON public.tbl_user USING btree (prefered_language_id);


--
-- TOC entry 2877 (class 1259 OID 29601)
-- Name: idx_3fccf306853cd175; Type: INDEX; Schema: public; Owner: joliquiz
--

CREATE INDEX idx_3fccf306853cd175 ON public.tbl_workout USING btree (quiz_id);


--
-- TOC entry 2878 (class 1259 OID 29600)
-- Name: idx_3fccf306cb944f1a; Type: INDEX; Schema: public; Owner: joliquiz
--

CREATE INDEX idx_3fccf306cb944f1a ON public.tbl_workout USING btree (student_id);


--
-- TOC entry 2867 (class 1259 OID 29747)
-- Name: idx_517fffec82f1baf4; Type: INDEX; Schema: public; Owner: joliquiz
--

CREATE INDEX idx_517fffec82f1baf4 ON public.tbl_category USING btree (language_id);


--
-- TOC entry 2864 (class 1259 OID 29573)
-- Name: idx_577b239a1e27f6bf; Type: INDEX; Schema: public; Owner: joliquiz
--

CREATE INDEX idx_577b239a1e27f6bf ON public.tbl_answer USING btree (question_id);


--
-- TOC entry 2896 (class 1259 OID 29646)
-- Name: idx_9e994d3b79d11bda; Type: INDEX; Schema: public; Owner: joliquiz
--

CREATE INDEX idx_9e994d3b79d11bda ON public.tbl_history_answer USING btree (question_history_id);


--
-- TOC entry 2884 (class 1259 OID 29616)
-- Name: idx_c91a858f12469de2; Type: INDEX; Schema: public; Owner: joliquiz
--

CREATE INDEX idx_c91a858f12469de2 ON public.tbl_quiz_category USING btree (category_id);


--
-- TOC entry 2885 (class 1259 OID 29615)
-- Name: idx_c91a858f853cd175; Type: INDEX; Schema: public; Owner: joliquiz
--

CREATE INDEX idx_c91a858f853cd175 ON public.tbl_quiz_category USING btree (quiz_id);


--
-- TOC entry 2873 (class 1259 OID 29593)
-- Name: idx_db45675f12469de2; Type: INDEX; Schema: public; Owner: joliquiz
--

CREATE INDEX idx_db45675f12469de2 ON public.tbl_question_category USING btree (category_id);


--
-- TOC entry 2874 (class 1259 OID 29592)
-- Name: idx_db45675f1e27f6bf; Type: INDEX; Schema: public; Owner: joliquiz
--

CREATE INDEX idx_db45675f1e27f6bf ON public.tbl_question_category USING btree (question_id);


--
-- TOC entry 2870 (class 1259 OID 29727)
-- Name: idx_e1c4af6382f1baf4; Type: INDEX; Schema: public; Owner: joliquiz
--

CREATE INDEX idx_e1c4af6382f1baf4 ON public.tbl_question USING btree (language_id);


--
-- TOC entry 2893 (class 1259 OID 29637)
-- Name: idx_fcd2a776a6cccfc9; Type: INDEX; Schema: public; Owner: joliquiz
--

CREATE INDEX idx_fcd2a776a6cccfc9 ON public.tbl_history_question USING btree (workout_id);


--
-- TOC entry 2891 (class 1259 OID 29626)
-- Name: uniq_38b383a1e7927c74; Type: INDEX; Schema: public; Owner: joliquiz
--

CREATE UNIQUE INDEX uniq_38b383a1e7927c74 ON public.tbl_user USING btree (email);


--
-- TOC entry 2892 (class 1259 OID 29625)
-- Name: uniq_38b383a1f85e0677; Type: INDEX; Schema: public; Owner: joliquiz
--

CREATE UNIQUE INDEX uniq_38b383a1f85e0677 ON public.tbl_user USING btree (username);


--
-- TOC entry 2901 (class 1259 OID 29705)
-- Name: uniq_83e89798734d08e1; Type: INDEX; Schema: public; Owner: joliquiz
--

CREATE UNIQUE INDEX uniq_83e89798734d08e1 ON public.tbl_language USING btree (english_name);


--
-- TOC entry 2909 (class 2606 OID 29728)
-- Name: tbl_quiz fk_1132af7a82f1baf4; Type: FK CONSTRAINT; Schema: public; Owner: joliquiz
--

ALTER TABLE ONLY public.tbl_quiz
    ADD CONSTRAINT fk_1132af7a82f1baf4 FOREIGN KEY (language_id) REFERENCES public.tbl_language(id);


--
-- TOC entry 2912 (class 2606 OID 29716)
-- Name: tbl_user fk_38b383a197e28a86; Type: FK CONSTRAINT; Schema: public; Owner: joliquiz
--

ALTER TABLE ONLY public.tbl_user
    ADD CONSTRAINT fk_38b383a197e28a86 FOREIGN KEY (prefered_language_id) REFERENCES public.tbl_language(id);


--
-- TOC entry 2908 (class 2606 OID 29667)
-- Name: tbl_workout fk_3fccf306853cd175; Type: FK CONSTRAINT; Schema: public; Owner: joliquiz
--

ALTER TABLE ONLY public.tbl_workout
    ADD CONSTRAINT fk_3fccf306853cd175 FOREIGN KEY (quiz_id) REFERENCES public.tbl_quiz(id);


--
-- TOC entry 2907 (class 2606 OID 29662)
-- Name: tbl_workout fk_3fccf306cb944f1a; Type: FK CONSTRAINT; Schema: public; Owner: joliquiz
--

ALTER TABLE ONLY public.tbl_workout
    ADD CONSTRAINT fk_3fccf306cb944f1a FOREIGN KEY (student_id) REFERENCES public.tbl_user(id);


--
-- TOC entry 2903 (class 2606 OID 29742)
-- Name: tbl_category fk_517fffec82f1baf4; Type: FK CONSTRAINT; Schema: public; Owner: joliquiz
--

ALTER TABLE ONLY public.tbl_category
    ADD CONSTRAINT fk_517fffec82f1baf4 FOREIGN KEY (language_id) REFERENCES public.tbl_language(id);


--
-- TOC entry 2902 (class 2606 OID 29647)
-- Name: tbl_answer fk_577b239a1e27f6bf; Type: FK CONSTRAINT; Schema: public; Owner: joliquiz
--

ALTER TABLE ONLY public.tbl_answer
    ADD CONSTRAINT fk_577b239a1e27f6bf FOREIGN KEY (question_id) REFERENCES public.tbl_question(id);


--
-- TOC entry 2914 (class 2606 OID 29687)
-- Name: tbl_history_answer fk_9e994d3b79d11bda; Type: FK CONSTRAINT; Schema: public; Owner: joliquiz
--

ALTER TABLE ONLY public.tbl_history_answer
    ADD CONSTRAINT fk_9e994d3b79d11bda FOREIGN KEY (question_history_id) REFERENCES public.tbl_history_question(id);


--
-- TOC entry 2911 (class 2606 OID 29677)
-- Name: tbl_quiz_category fk_c91a858f12469de2; Type: FK CONSTRAINT; Schema: public; Owner: joliquiz
--

ALTER TABLE ONLY public.tbl_quiz_category
    ADD CONSTRAINT fk_c91a858f12469de2 FOREIGN KEY (category_id) REFERENCES public.tbl_category(id) ON DELETE CASCADE;


--
-- TOC entry 2910 (class 2606 OID 29672)
-- Name: tbl_quiz_category fk_c91a858f853cd175; Type: FK CONSTRAINT; Schema: public; Owner: joliquiz
--

ALTER TABLE ONLY public.tbl_quiz_category
    ADD CONSTRAINT fk_c91a858f853cd175 FOREIGN KEY (quiz_id) REFERENCES public.tbl_quiz(id) ON DELETE CASCADE;


--
-- TOC entry 2906 (class 2606 OID 29657)
-- Name: tbl_question_category fk_db45675f12469de2; Type: FK CONSTRAINT; Schema: public; Owner: joliquiz
--

ALTER TABLE ONLY public.tbl_question_category
    ADD CONSTRAINT fk_db45675f12469de2 FOREIGN KEY (category_id) REFERENCES public.tbl_category(id) ON DELETE CASCADE;


--
-- TOC entry 2905 (class 2606 OID 29652)
-- Name: tbl_question_category fk_db45675f1e27f6bf; Type: FK CONSTRAINT; Schema: public; Owner: joliquiz
--

ALTER TABLE ONLY public.tbl_question_category
    ADD CONSTRAINT fk_db45675f1e27f6bf FOREIGN KEY (question_id) REFERENCES public.tbl_question(id) ON DELETE CASCADE;


--
-- TOC entry 2904 (class 2606 OID 29722)
-- Name: tbl_question fk_e1c4af6382f1baf4; Type: FK CONSTRAINT; Schema: public; Owner: joliquiz
--

ALTER TABLE ONLY public.tbl_question
    ADD CONSTRAINT fk_e1c4af6382f1baf4 FOREIGN KEY (language_id) REFERENCES public.tbl_language(id);


--
-- TOC entry 2913 (class 2606 OID 29682)
-- Name: tbl_history_question fk_fcd2a776a6cccfc9; Type: FK CONSTRAINT; Schema: public; Owner: joliquiz
--

ALTER TABLE ONLY public.tbl_history_question
    ADD CONSTRAINT fk_fcd2a776a6cccfc9 FOREIGN KEY (workout_id) REFERENCES public.tbl_workout(id);


-- Completed on 2018-07-17 15:41:32 CEST

--
-- PostgreSQL database dump complete
--

