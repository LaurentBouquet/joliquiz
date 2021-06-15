
INSERT INTO tbl_user (id, username, email, password, is_active, roles, prefered_language_id, token, password_requested_at) VALUES
(1, 'superadmin', 'superadmin@domain.tld', 'TODO', 1, 'a:1:{i:0;s:9:\"ROLE_SUPER_ADMIN\";}', NULL, NULL, NULL),
(2, 'teacher', 'teacher@domain.tld', 'TODO', 1, 'a:1:{i:0;s:9:\"ROLE_TEACHER\";}', NULL, NULL, NULL),
(3, 'user', 'user@domain.tld', 'TODO', 1, 'a:1:{i:0;s:9:\"ROLE_USER\";}', NULL, NULL, NULL),
(4, 'admin', 'admin@domain.tld', 'TODO', 1, 'a:1:{i:0;s:9:\"ROLE_ADMIN\";}', NULL, NULL, NULL);
