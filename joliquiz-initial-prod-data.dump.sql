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

DROP INDEX public.uniq_38b383a1f85e0677;
DROP INDEX public.uniq_38b383a1e7927c74;
ALTER TABLE ONLY public.tbl_user DROP CONSTRAINT tbl_user_pkey;
ALTER TABLE ONLY public.tbl_category DROP CONSTRAINT tbl_category_pkey;
ALTER TABLE ONLY public.migration_versions DROP CONSTRAINT migration_versions_pkey;
DROP SEQUENCE public.tbl_user_id_seq;
DROP TABLE public.tbl_user;
DROP SEQUENCE public.tbl_category_id_seq;
DROP TABLE public.tbl_category;
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
-- Data for Name: migration_versions; Type: TABLE DATA; Schema: public; Owner: -
--

COPY public.migration_versions (version) FROM stdin;
20180629132358
20180629142824
\.


--
-- Data for Name: tbl_category; Type: TABLE DATA; Schema: public; Owner: -
--

COPY public.tbl_category (id, shortname, longname) FROM stdin;
\.


--
-- Data for Name: tbl_user; Type: TABLE DATA; Schema: public; Owner: -
--

COPY public.tbl_user (id, username, email, password, is_active, roles) FROM stdin;
1	superadmin	superadmin@domain.tld	$2y$13$9DB9OpIPKemSVYSUeE8Rhu51jyg0xtqk.OqhF5lGjyxOT7kOcjjhG	t	a:2:{i:0;s:16:"ROLE_SUPER_ADMIN";i:1;s:9:"ROLE_USER";}
2	admin	admin@domain.tld	$2y$13$2KFgTNZAHXQ/wAJx8gfAXunPtB19274fX1fMVvMcNEHAKXldU90DG	t	a:2:{i:0;s:10:"ROLE_ADMIN";i:1;s:9:"ROLE_USER";}
3	user	user@domain.tld	$2y$13$7ETfU2XuwSHvMWrI.3DIHeFjaZOQcfY4wOcYpkXAwh1jxdP/Cy3Am	t	a:1:{i:0;s:9:"ROLE_USER";}
\.


--
-- Name: tbl_category_id_seq; Type: SEQUENCE SET; Schema: public; Owner: -
--

SELECT pg_catalog.setval('public.tbl_category_id_seq', 1, false);


--
-- Name: tbl_user_id_seq; Type: SEQUENCE SET; Schema: public; Owner: -
--

SELECT pg_catalog.setval('public.tbl_user_id_seq', 3, true);


--
-- Name: migration_versions migration_versions_pkey; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.migration_versions
    ADD CONSTRAINT migration_versions_pkey PRIMARY KEY (version);


--
-- Name: tbl_category tbl_category_pkey; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.tbl_category
    ADD CONSTRAINT tbl_category_pkey PRIMARY KEY (id);


--
-- Name: tbl_user tbl_user_pkey; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.tbl_user
    ADD CONSTRAINT tbl_user_pkey PRIMARY KEY (id);


--
-- Name: uniq_38b383a1e7927c74; Type: INDEX; Schema: public; Owner: -
--

CREATE UNIQUE INDEX uniq_38b383a1e7927c74 ON public.tbl_user USING btree (email);


--
-- Name: uniq_38b383a1f85e0677; Type: INDEX; Schema: public; Owner: -
--

CREATE UNIQUE INDEX uniq_38b383a1f85e0677 ON public.tbl_user USING btree (username);


--
-- PostgreSQL database dump complete
--

