--
-- PostgreSQL database dump
--

-- Dumped from database version 10.4 (Ubuntu 10.4-0ubuntu0.18.04)
-- Dumped by pg_dump version 10.4 (Ubuntu 10.4-0ubuntu0.18.04)

INSERT INTO public.tbl_user (id, username, email, password, is_active, roles) VALUES (1, 'superadmin', 'superadmin@domain.tld', '$2y$13$S8Urw2D2TfxaOGp3fjP5CuZdMeUTlOvpMEeLm4EDCXXFgcQro6OIW', true, 'a:2:{i:0;s:16:"ROLE_SUPER_ADMIN";i:1;s:9:"ROLE_USER";}');
INSERT INTO public.tbl_user (id, username, email, password, is_active, roles) VALUES (3, 'teacher', 'teacher@domain.tld', '$2y$13$ETcERMGM2/RfX5ApgEXlaeutnmpj/17JFDPLWG5nzJ3xciF01uk32', true, 'a:2:{i:0;s:12:"ROLE_TEACHER";i:1;s:9:"ROLE_USER";}');
INSERT INTO public.tbl_user (id, username, email, password, is_active, roles) VALUES (4, 'user', 'user@domain.tld', '$2y$13$Y.XPv.owBQ73eZ7juzVW.OPTZTMAP67MG9mZUM9BvAfSnJv3X7Qre', true, 'a:1:{i:0;s:9:"ROLE_USER";}');
INSERT INTO public.tbl_user (id, username, email, password, is_active, roles) VALUES (2, 'admin', 'admin@domain.tld', '$2y$13$qy/peqCnpCTk20VUJBuM9.axawgEeqXHL2o6YVVmKp6FG4z17M5YG', true, 'a:2:{i:1;s:10:"ROLE_ADMIN";i:2;s:9:"ROLE_USER";}');

SELECT pg_catalog.setval('public.tbl_user_id_seq', 4, true);
