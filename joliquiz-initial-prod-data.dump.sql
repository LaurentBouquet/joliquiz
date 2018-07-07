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
ALTER TABLE ONLY public.tbl_quiz_category DROP CONSTRAINT fk_c91a858f853cd175;
ALTER TABLE ONLY public.tbl_quiz_category DROP CONSTRAINT fk_c91a858f12469de2;
ALTER TABLE ONLY public.tbl_history_answer DROP CONSTRAINT fk_9e994d3b79d11bda;
ALTER TABLE ONLY public.tbl_answer DROP CONSTRAINT fk_577b239a1e27f6bf;
ALTER TABLE ONLY public.tbl_queston_category DROP CONSTRAINT fk_483bda3f1e27f6bf;
ALTER TABLE ONLY public.tbl_queston_category DROP CONSTRAINT fk_483bda3f12469de2;
ALTER TABLE ONLY public.tbl_workout DROP CONSTRAINT fk_3fccf306cb944f1a;
ALTER TABLE ONLY public.tbl_workout DROP CONSTRAINT fk_3fccf306853cd175;
DROP INDEX public.uniq_38b383a1f85e0677;
DROP INDEX public.uniq_38b383a1e7927c74;
DROP INDEX public.idx_fcd2a776a6cccfc9;
DROP INDEX public.idx_c91a858f853cd175;
DROP INDEX public.idx_c91a858f12469de2;
DROP INDEX public.idx_9e994d3b79d11bda;
DROP INDEX public.idx_577b239a1e27f6bf;
DROP INDEX public.idx_483bda3f1e27f6bf;
DROP INDEX public.idx_483bda3f12469de2;
DROP INDEX public.idx_3fccf306cb944f1a;
DROP INDEX public.idx_3fccf306853cd175;
ALTER TABLE ONLY public.tbl_workout DROP CONSTRAINT tbl_workout_pkey;
ALTER TABLE ONLY public.tbl_user DROP CONSTRAINT tbl_user_pkey;
ALTER TABLE ONLY public.tbl_quiz DROP CONSTRAINT tbl_quiz_pkey;
ALTER TABLE ONLY public.tbl_quiz_category DROP CONSTRAINT tbl_quiz_category_pkey;
ALTER TABLE ONLY public.tbl_queston_category DROP CONSTRAINT tbl_queston_category_pkey;
ALTER TABLE ONLY public.tbl_question DROP CONSTRAINT tbl_question_pkey;
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
DROP TABLE public.tbl_queston_category;
DROP SEQUENCE public.tbl_question_id_seq;
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
    date_time timestamp(0) without time zone NOT NULL,
    answer_id integer NOT NULL,
    answer_text text NOT NULL,
    answer_correct boolean NOT NULL,
    correct_given boolean NOT NULL,
    answer_succes boolean NOT NULL
);


--
-- Name: COLUMN tbl_history_answer.date_time; Type: COMMENT; Schema: public; Owner: -
--

COMMENT ON COLUMN public.tbl_history_answer.date_time IS '(DC2Type:datetime_immutable)';


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
    date_time timestamp(0) without time zone NOT NULL,
    question_id integer NOT NULL,
    question_text text NOT NULL,
    completed boolean NOT NULL,
    question_success boolean,
    duration character(255) DEFAULT NULL::bpchar
);


--
-- Name: COLUMN tbl_history_question.date_time; Type: COMMENT; Schema: public; Owner: -
--

COMMENT ON COLUMN public.tbl_history_question.date_time IS '(DC2Type:datetime_immutable)';


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
-- Name: tbl_question_id_seq; Type: SEQUENCE; Schema: public; Owner: -
--

CREATE SEQUENCE public.tbl_question_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--
-- Name: tbl_queston_category; Type: TABLE; Schema: public; Owner: -
--

CREATE TABLE public.tbl_queston_category (
    question_id integer NOT NULL,
    category_id integer NOT NULL
);


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
    updated_at timestamp(0) without time zone NOT NULL
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
    ended_at timestamp(0) without time zone NOT NULL,
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
20180707145851
\.


--
-- Data for Name: tbl_answer; Type: TABLE DATA; Schema: public; Owner: -
--

COPY public.tbl_answer (id, question_id, text, correct) FROM stdin;
1	1	debug	f
2	1	alert	f
3	1	severe	t
4	1	warning	f
5	2	log	f
6	2	set	t
7	2	dump	t
8	2	print	t
9	3	( name )	f
10	3	[ name ]	f
11	3	{{ name }}	f
12	3	{ name }	t
13	4	Psr-0	t
14	4	Psr-11	f
15	4	Psr-4	t
16	4	Psr-6	f
17	5	$foo->bar($arg1);	t
18	5	$Foo->bar($arg1);	f
19	5	class Foo extends Bar implements FooInterface	t
20	5	class foo extends Bar implements FooInterface	f
21	6	myMethod();	t
22	6	MyMethod();	f
23	6	my_method();	f
24	6	My_Method();	f
25	7	Psr-0	f
26	7	Psr-6	t
27	7	Psr-16	t
28	7	Psr-1	f
29	7	Psr-2	f
30	8	Psr-1 (Basic Coding Standard)	f
31	8	Psr-6 (Caching Interface)	f
32	8	Psr-0 (Autoloading Standard)	t
33	8	Psr-11 (Container Interface)	f
34	9	<argument id="my.custom.service" />	f
35	9	<argument type="argument" id="my.custom.service" />	f
36	9	<argument type="service" id="my.custom.service" />	t
37	9	<service id="my.custom.service" />	f
38	10	constructor	t
39	10	property	t
40	10	method	t
41	10	constant	f
42	11	<service id="my.service" />	f
43	11	<argument type="service" id="my.service" />	t
44	11	<method setter="setService"><argument type="service" id="my.service" /></method>	f
45	11	<call method="setService"><argument type="service" id="my.service" /></call>	t
46	12	setScope($name)	f
47	12	getScope($name)	f
48	12	addScope($name)	t
49	12	enterScope($name)	t
50	13	proxy_service: true	f
51	13	lazy: true	t
52	13	proxy: enabled	f
53	13	proxy: true	f
54	14	modify()	f
55	14	processContainer()	f
56	14	process()	t
57	14	modifyContainer()	f
58	15	$container->getDefinition('my.service')->setClass('My\\Service\\Class')	t
59	15	$container->getService('my.service')->setDefinition('My\\Service\\Class')	f
60	15	$container->getDefinition('my.service')->setService('My\\Service\\Class')	f
61	15	$container->getService('my.service')->setClass('My\\Service\\Class')	f
62	16	$container->getDefinition('my.service')	t
63	16	$container->findDefinition('my.service')	t
64	16	$container->retrieveDefinition('my.service')	f
65	16	$container->getService('my.service')	f
66	17	PassConfig::TYPE_BEFORE_OPTIMIZATION	t
67	17	PassConfig::TYPE_OPTIMIZE	t
68	17	PassConfig::TYPE_BEFORE_REMOVING	t
69	17	PassConfig::TYPE_REMOVE	t
70	17	PassConfig::TYPE_AFTER_COMPILING	f
71	18	appDevDebugProjectContainer.php	t
72	18	appDevProjectContainer.php	f
73	18	appDevDebugProjectServiceContainer	f
74	18	appDevDebugContainer	f
75	19	true	t
76	19	false	f
77	20	arguments: ['@app.mailer']	t
78	20	arguments: ['%app.mailer%']	f
79	20	arguments: ['app.mailer']	f
80	21	arguments: ['@mailer.transport']	f
81	21	arguments: ['%mailer.transport%']	t
82	21	arguments: ['mailer.transport']	f
83	22	type: private	f
84	22	scope: private	f
85	22	public: false	t
86	22	private: true	f
87	23	true	t
88	23	false	f
89	24	{{ app.session.flashbag('success') }}	f
90	24	{{ app.session.flashbag.get('success') }}	f
91	24	{% for message in app.session.flashbag('success') %} {{ message }} {% endfor %}	f
92	24	{% for message in app.session.flashbag.get('success') %} {{ message }} {% endfor %}	t
93	25	{{ 'my string'|uppercase }}	f
94	25	{{ 'my string'|upper }}	t
95	25	{{ 'my string'|capitalize }}	f
96	25	{{ 'my string'|big }}	f
97	26	Yes	t
98	26	No	f
99	27	{{ var myVariable = 'example' }}	f
100	27	{% var myVariable = 'example' %}	f
101	27	{{ set myVariable = 'example' }}	f
102	27	{% set myVariable = 'example' %}	t
103	28	getExtensions()	f
104	28	getFunctions()	t
105	28	getOperators()	f
106	28	getFunctionList()	f
107	29	ends with	f
108	29	matches	f
109	29	in	f
110	29	contains	t
111	30	{% %}	t
112	30	{{ }}	f
113	30	{# #}	f
114	30	{ }	f
115	31	{% if is_granted('ROLE_TEST') %}	t
116	31	{% if is_authorized('ROLE_TEST') %}	f
117	31	{% if has_role('ROLE_TEST') %}	f
118	31	{% if has_access('ROLE_TEST') %}	f
119	32	{{ app.user.username }}	t
120	32	{{ app.user.name }}	f
121	32	{{ app.user.id }}	f
122	32	{{ app.user }}	f
123	33	app/Resources/TwigBundle/views/Exception/error.html.twig	t
124	33	app/Resources/TwigBundle/views/Exception/error404.html.twig	t
125	33	app/Resources/views/Exception/error.html.twig	f
126	33	app/Resources/TwigBundle/views/Exception/error.twig	f
127	33	app/Resources/views/Exception/error.twig	f
128	34	spaceless	f
129	34	gzip	t
130	34	operators	f
131	35	spaceless	f
132	35	raw2	f
133	35	escape with some special parameters	f
134	35	noparse	f
135	35	verbatim	t
136	35	raw	f
137	36	is used to fill strings with random characters in a batch	f
138	36	renders a vector out of an element value given	f
139	36	is used to parse a string and output the string characters in an for loop	f
140	36	returns a list of lists with a given number of items out of the set	t
141	37	$twig->setCompiler($compiler)	t
142	37	$twig->load($compiler)	f
143	37	$twig->compile($compiler)	f
144	37	$twig->compileWith($compiler)	f
145	38	I can implement Twig_LoaderInterface, Twig_ExistsLoaderInterface into a combined loader and load everything at once	f
146	38	I can implement DatabaseTwigLoader extending Twig_LoaderInterface and Twig_ExistsLoaderInterface and load templates using TwigMainLoader	f
147	38	I can do as b but use the Twig_Loader_Chain to chain loaders and set it to the Context	f
148	38	I can do as c but rather than setting the loaders into Context I initialize Twig_Environment with the chain loader	t
149	38	is not currently possible but there is a PR for that	f
150	39	needsLoader()	t
151	39	needsEnvironment()	f
152	39	needsContext()	f
153	39	getSafe() and getPreEscape()	f
154	39	1 and 3	f
155	40	-255 to 255	f
156	40	-70 to 70	f
157	40	0 - 40	f
158	40	-10 to 10	t
159	40	it is fixed	f
160	41	setArguments, getArguments	f
161	41	isValid()	f
162	41	setPriority(), getPriority()	f
163	41	none of these options	t
164	42	first and last functions work in the same way as the random filter	f
165	42	first and last functions are great to extract the first and the last element of an array	f
166	42	random filter when used without arguments is like mt_rand	f
167	42	random function when passed a string splits the string and outputs a single character	t
168	42	when random function receives a number it treats it as the top value of the random series it outputs	t
169	43	If person has a property address-main, I can access it with person.getAddressMain()	f
170	43	If person has a property address_main, I better access it with person.['address_main']	f
171	43	a and b are possible	f
172	43	I have to create a special getter for accessing the property above	f
173	43	no, the [] operand works in any situation	f
174	43	get_attribute(person, 'address-main') does it	f
175	43	none of the above, Twig comes with a special function for this	t
176	44	you can chain filters with filters and functions with functions but not mix them	f
177	44	you can chain anything	f
178	44	Escaping is enabled by default	t
179	44	you can access global vars only provided by Symfony on TwigBundle	f
180	44	there is only three global vars that come by default _self, _superglobals, _charset	f
181	45	Yes	t
182	45	No	f
183	46	Yes	t
184	46	No	f
185	47	hinclude_render(controller(...))	f
186	47	render_hinclude(controller(...))	t
187	47	none of above	f
188	48	True	f
189	48	False	t
190	49	{% %}	f
191	49	{{ }}	t
192	49	{# #}	f
193	49	{ }	f
194	50	{% %}	f
195	50	{{ }}	f
196	50	{# #}	t
197	50	{ }	f
198	51	<include:esi src="http://..." />	f
199	51	<esi:include src="http://..." />	t
200	51	<include src="http://..." />	f
201	51	<esi src="http://..." />	f
202	52	cache_esi()	f
203	52	esi_cache()	f
204	52	esi_render()	f
205	52	render_esi()	t
206	53	Expires	t
207	53	Last-Modified	f
208	53	ETag	f
209	53	Cache-Control	t
210	53	Cookie	f
211	54	Expires	f
212	54	Last-Modified	t
213	54	ETag	t
214	54	Cache-Control	f
215	54	Cookie	f
216	55	304	t
217	55	200	f
218	55	202	f
219	55	300	f
220	55	305	f
221	56	True	t
222	56	False	f
223	57	one year	t
224	57	one month	f
225	57	one week	f
226	57	There is not limit	f
227	58	True	t
228	58	False	f
229	59	True	f
230	59	False	t
231	60	Add a "domain" attribute	f
232	60	Add a "host" attribute	t
233	60	Add a "path" attribute	f
234	60	Add a "subdomain" attribute	f
235	61	$_route	t
236	61	$_controller	f
237	61	$_method	f
238	61	$_action	f
239	62	schemes="https"	t
240	62	https="true"	f
241	62	protocol="https"	f
242	62	ensure="https"	f
243	63	_controller, _locale, _schemes	f
244	63	_route, _controller, _action	f
245	63	_controller, _locale, _format	t
246	63	_locale, _format and another one.	f
247	64	By using UrlGeneratorInterface::ABSOLUTE_URL as third parameter for generateUrl	t
248	64	generateUrl generate absolute URL by default	f
249	64	$this->generateAbsoluteUrl()	f
250	65	FrameworkBundle:Controller:template	f
251	65	FrameworkBundle:Template:action	f
252	65	FrameworkBundle:Template:render	f
253	65	FrameworkBundle:Template:template	t
254	66	Psr\\LoggerInterface	f
255	66	Psr\\Log\\LoggerInterface	t
256	66	Symfony\\Bridge\\Monolog\\LoggerInterface	f
257	66	Symfony\\Bridge\\Monolog\\Log\\LoggerInterface	f
258	67	app/cache/dev/appDevDebugProjectContainer.php	t
259	67	app/cache/dev/appDebugProjectContainer.php	f
260	67	app/cache/dev/appDevProjectDebugContainer.php	f
261	67	app/cache/dev/appProjectContainer.php	f
262	68	app/cache/dev/appDevUrlGenerator.php	t
263	68	app/cache/dev/appDevGeneratorUrl.php	f
264	68	app/cache/dev/appDevUrlDump.php	f
265	68	app/cache/dev/appDevDumpedUrl.php	f
266	69	public static function enable($errorReportingLevel = null, $displayErrors = true)	t
267	69	public static function create($errorReportingLevel = null, $displayErrors = true)	f
268	69	public static function load($errorReportingLevel = null, $displayErrors = true)	f
269	69	public static function start($errorReportingLevel = null, $displayErrors = true)	f
270	70	hinclude: enabled	f
271	70	fragments: {path:/_fragment}	f
272	70	none of the above	t
273	71	Symfony\\Component\\CssSelector\\CssSelector	t
274	71	Symfony\\Component\\BrowserKit\\CssSelector	f
275	71	Symfony\\Component\\DomCrawler\\CssSelector	f
276	71	Symfony\\Bundle\\FrameworkBundle\\CssSelector	f
277	72	False	t
278	72	True	f
279	73	MyTestCommand.php	t
280	73	CommandMyTest.php	f
281	73	MyTestConsoleCommand.php	t
282	73	MyTestCommandConsole.php	f
283	74	@AcmeTestBundle:Question:Item/list.html.twig	t
284	74	@AcmeTestBundle:Question:Item:list.html.twig	f
285	74	@AcmeTestBundle:Question/Item/list.html.twig	f
286	74	@Acme:TestBundle:Question:Item/list.html.twig	f
287	74	@Acme:TestBundle:Question/Item/list.html.twig	f
288	75	fos_user.listener.email_confirmation	t
289	75	sensio_framework_extra.PSR7.http_message_factory	f
290	75	sensio_distribution.security_checker	t
291	76	Symfony\\Component\\Validator\\GroupValidationInterface	f
292	76	Symfony\\Component\\Validator\\GroupValidationProviderInterface	f
293	76	Symfony\\Component\\Validator\\GroupProviderInterface	f
294	76	Symfony\\Component\\Validator\\GroupSequenceProviderInterface	t
295	77	$validator->validate($object, $constraint)	t
296	77	$validator->validateValue($object, $constraint)	f
297	77	$validator->isValid($object, $constraint)	f
298	77	$validator->validation($object, $constraint)	f
299	78	True	t
300	78	False	f
301	79	True	f
302	79	False	t
303	80	Annotations mapping are enabled by default	f
304	80	UniqueEntity is provided by Doctrine Bundle	f
305	80	We can validate partial object with @Assert\\GroupSequence	t
306	80	All assertions above are valid	f
307	81	Blank	t
308	81	IdenticalTo	t
309	81	EqualTo	t
310	81	SameAs	f
311	82	@Assert\\Choice({"male", "female", "other"})	t
312	82	@Assert\\Choice(choices = {"male", "female", "other"})	t
313	82	@Assert\\Choices({"male", "female", "other"})	f
314	82	@Assert\\Choices(choices = {"male", "female", "other"})	f
315	83	pass one or more group names as the third argument of $validator->validate()	t
316	83	pass one or more group names as the second argument of $validator->validate()	f
317	83	use $validator->setValidationGroups(array)	f
318	84	SIGINT	f
319	84	SIGKILL	t
320	84	Process::STOP	f
321	84	9	t
322	85	"/path/to/file.yml"	f
323	85	file_get_contents("/path/to/file.yml")	t
324	85	Both are valid	f
325	86	QuestionHelper	f
326	86	TableHelper	f
327	86	FileHelper	t
328	86	DialogHelper	t
329	87	ConsoleEvents::COMMAND	f
330	87	ConsoleEvents::TERMINATE	f
331	87	ConsoleEvents::EXCEPTION	f
332	87	ConsoleEvents::LAUNCH	t
333	88	php app/console -s	t
334	88	php app/console --interactive	f
335	88	no one	f
336	89	ConsoleEvents::COMMAND	t
337	89	ConsoleEvents::TERMINATE	t
338	89	ConsoleEvents::EXCEPTION	t
339	89	ConsoleEvents::BEFORE	f
340	89	ConsoleEvents::AFTER	f
341	90	Symfony\\Component\\Console\\OutputFormatterStyle	t
342	90	Symfony\\Component\\Console\\OutputStyleFormatter	f
343	90	Symfony\\Component\\Console\\OutputStyle	f
344	90	Symfony\\Component\\Console\\OutputFormatter	f
345	91	php app/console bundle:generate	f
346	91	php app/console generate:bundle	t
347	91	php app/console create:bundle	f
348	91	php app/console bundle:create	f
349	92	No argument can be passed	f
350	92	A regexp search filter	f
351	92	A route limit	f
352	92	A route name	t
353	93	php app/console twig:lint	t
354	93	php app/console twig:check	f
355	93	php app/console twig:validate	f
356	93	php app/console twig:syntax	f
357	94	php app/console cache:clear	t
358	94	php app/console clear:cache	f
359	94	php app/console cache:clean	f
360	94	php app/console cache:invalidate	f
361	95	command.console	f
362	95	console.command	t
363	95	console	f
364	95	command	f
365	96	php app/console doctrine:mapping:info	t
366	96	php app/console doctrine:mapping:import	f
367	96	php app/console doctrine:mapping:info --all	f
368	96	php app/console doctrine:mapping:import --all	f
369	97	php app/console doctrine:schema:validate	t
370	97	php app/console doctrine:schema:update --dump-sql	f
371	97	php app/console doctrine:schema:update --force	f
372	97	php app/console doctrine:mapping:convert	f
373	97	php app/console doctrine:mapping:import	f
374	98	php app/console dump:config acme	f
375	98	php app/console config:dump-reference acme	t
376	98	php app/console config:dump AcmeBundle	t
377	98	php app/console config:dump-reference AcmeBundle	t
378	99	Yes	t
379	99	No	f
380	100	Yes	f
381	100	No	t
382	101	php app/console doctrine:generate:entity	t
383	101	php app/console generate:entity	f
384	101	php app/console database:create:entity	f
385	101	php app/console doctrine:create:entity	f
386	102	php app/console doctrine:schema:update	t
387	102	php app/console doctrine:entity:update	f
388	102	php app/console doctrine:entity --create	f
389	102	php app/console doctrine:schema:create	f
390	103	php app/console doctrine:fixtures:load	t
391	103	php app/console doctrine:load:fixtures	f
392	103	php app/console doctrine:fixtures	f
393	103	php app/console doctrine:fixtures:import	f
394	104	OutputInterface::VERBOSITY_QUIET	t
395	104	OutputInterface::VERBOSITY_NORMAL	t
396	104	OutputInterface::VERBOSITY_VERBOSE	t
397	104	OutputInterface::VERBOSITY_VERY_VERBOSE	t
398	104	OutputInterface::DEBUG	f
399	105	InputArgument::DEFAULT	f
400	105	InputArgument::IS_ARRAY	t
401	105	InputArgument::LIST	f
402	105	InputArgument::ENUM	f
403	106	initialize() and configure() are invoked before the ConsoleEvents::COMMAND	f
404	106	ConsoleEvents::TERMINATE is dispatched even when an exception is thrown	t
405	106	initialize has InputInterface, OutputInterface parameters	t
406	106	ConsoleEvents can be disabled	t
407	107	configure()	t
408	107	execute(InputInterface $input, OutputInterface $output)	t
409	107	interact()	f
410	107	initialize()	f
411	108	Symfony\\Component\\Console\\Question\\Question	t
412	108	Symfony\\Component\\Console\\Question\\ChoiceQuestion	t
413	108	Symfony\\Component\\Console\\Question\\ConfirmationQuestion	t
414	108	Symfony\\Component\\Console\\Question\\SelectQuestion	f
415	108	Symfony\\Component\\Console\\Question\\ValidQuestion	f
416	109	$command->execute($input, $output);	f
417	109	$command->run($input, $output);	t
418	109	$command->call($input, $output);	f
419	109	$command->forward($input, $output);	f
420	110	setUp()	t
421	110	__construct()	f
422	110	shutdown()	f
423	110	tearDown()	t
424	111	$this->getMock('My\\Class')->disableOriginalConstructor()->getMock()	f
425	111	$this->disableOriginalConstructor('My\\Class')	f
426	111	$this->getMockBuilder('My\\Class')->disableOriginalConstructor()->getMock()	t
427	111	$this->getMockBuilder('My\\Class')->getMock()->disableConstructor()	f
428	112	Symfony\\Component\\Console\\Tester\\Command	f
429	112	Symfony\\Component\\Console\\Tester\\CommandTester	t
430	112	Symfony\\Component\\Console\\Tester\\CommandUnitTester	f
431	112	Symfony\\Component\\Console\\Tester\\CommandUnit	f
432	113	$mock->expects($this->at(1))	t
433	113	$mock->expects($this->at(2))	f
434	113	$mock->expects($this->exactly(2))	f
435	113	$mock->expects($this->on(2))	f
436	114	$this->setExpectedException('MyException')	t
437	114	$this->setExceptionExpected('MyException')	f
438	114	$this->expectException('MyException')	f
439	114	$this->setExpected('MyException')	f
440	115	phpunit -c app/	t
441	115	phpunit app/	f
442	115	phpunit -c	f
443	115	phpunit	f
444	116	Tests/	f
445	116	Tests/Controller/	t
446	116	Tests/Controllers/	f
447	116	Controller/	f
448	117	back()	t
449	117	forward()	t
450	117	insulate()	t
451	117	restart()	t
452	117	next()	f
453	118	swiftmailer.disable_delivery: true	t
454	118	swiftmailer.delivery: false	f
455	118	swiftmailer.enable_delivery: false	f
456	118	swiftmailer.disable: true	f
457	119	$client->redirect()	f
458	119	$client->followRedirect()	t
459	119	$client->followRedirects()	t
460	119	$client->redirectAll()	f
461	120	<!	f
462	120	<%	f
463	120	<?	t
464	120	<?=	f
465	121	!	f
466	121	**	f
467	121	<	t
468	121	and	f
469	122	use function foo;	f
470	122	use Myapp\\Utils\\Bar\\foo;	f
471	122	use function Myapp\\Utils\\Bar\\foo;	t
472	122	use Utils\\Bar\\Foo;	f
473	123	True	f
474	123	False	t
475	124	SOAP and XML-RPC	t
476	124	REST and SOAP	f
477	124	Corba and XML-RPC	f
478	124	XML-RPC and REST	f
479	125	case insensitive and case sensitive	f
480	125	case sensitive and case sensitive	f
481	125	case sensitive and case insensitive	t
482	125	case insensitive and case insensitive	f
483	126	Yes, in all cases	f
484	126	No, in all cases	t
485	126	Yes, but it depends on the function scope	f
486	126	Yes, except when it is an anonymous function	f
487	127	In PHP>=5.6 argument lists may include the ... token to denote that the function accepts a variable number of arguments	f
488	127	A function name, as other labels in PHP, must match the following regular expression: [a-zA-Z_-ÿ][a-zA-Z0-9_-ÿ]*	f
489	127	Variable functions work with language constructs such as echo, print, unset(), isset(), empty(), include or require	t
490	127	Assigning a closure (anonymous function) to a variable uses the same syntax as any other assignment, including the trailing semicolon	f
491	128	function &foo() {...}	t
492	128	function $foo() {...}	f
493	128	function %foo() {...}	f
494	128	function $$foo() {...}	f
495	129	abstract	t
496	129	final	f
497	129	protected	f
498	129	incomplete	f
499	129	implements	f
500	130	PSR-0	f
501	130	PSR-1	t
502	130	PSR-2	t
503	130	PSR-3	f
504	130	PSR-4	f
505	131	Traits	t
506	131	Object Cloning	f
507	131	ReflectionClass	f
508	131	Inheritance	f
509	132	True	t
510	132	False	f
511	133	True	f
512	133	False	t
513	134	Add an option 'render' => 'input'	f
514	134	Add an option 'widget' => 'text'	f
515	134	Add an option 'widget' => 'single_text'	t
516	134	Add an option 'widget' => 'input'	f
517	135	$form->bindRequest($request)	f
518	135	$form->handleRequest($request)	t
519	135	$form->handle($request)	f
520	135	$form->request($request)	f
521	136	$form->getView()	f
522	136	$form->renderView()	f
523	136	$form->view()	f
524	136	$form->createView()	t
525	137	{{ form_item(form.field) }}	f
526	137	{{ form_render(form.field) }}	f
527	137	{{ form_widget(form.field) }}	t
528	137	{{ form_view(form.field) }}	f
529	138	$formFactory->addExtension(new CsrfExtension($csrfProvider));	t
530	138	$formFactory->setExtension(new CsrfExtension($csrfProvider));	f
531	138	$formFactory->addCsrfExtension(new CsrfExtension($csrfProvider));	f
532	138	$formFactory->addExtension(new Csrf($csrfProvider));	f
533	139	csrf_field_name	t
534	139	csrf_fieldname	f
535	139	csrf_field	f
536	139	csrf_name	f
537	140	Yes	f
538	140	No	t
539	141	validation_groups	t
540	141	groups_validation	f
541	141	validator_groups	f
542	141	groups_validator	f
543	142	FormEvents::PRE_SET_DATA	t
544	142	FormEvents::SUBMIT	t
545	142	FormEvents::POST_SET_DATA	t
546	142	FormEvents::POST_REQUEST	f
547	143	Model data	t
548	143	Normalized data	f
549	143	Request data	f
550	143	View data	f
551	144	Model data	f
552	144	Normalized data	f
553	144	Request data	t
554	144	View data	f
555	145	money	f
556	145	currency	f
557	145	textarea	t
558	145	surname	f
559	146	date	t
560	146	datetime	t
561	146	time	t
562	146	timestamp	f
563	147	Using option 'constraints' in $formBuilder->add()	t
564	147	Passing constraint instance in $this->createFormBuilder()	f
565	147	Invoke $formBuilder->setConstraints() method	f
566	147	Add an annotation in model	t
567	148	True	f
568	148	False	t
569	149	$this->createFormBuilder($defaults)	t
570	149	$this->createFormBuilder(null, $defaults)	f
571	149	$this->createFormBuilder()->setDefaults($defaults)	f
572	149	$this->createFormBuilder()->addDefaults($defaults)	f
573	150	Symfony\\Component\\Form\\FormType	f
574	150	Symfony\\Component\\Form\\AbstractType	t
575	150	Symfony\\Component\\Form\\AbstractFormType	f
576	151	$builder->add('extra', null, ['mapped' => false])	t
577	151	$builder->add('extra', 'hidden', ['mapped' => false])	t
578	151	$builder->add('extra', null, ['validation' => false]	f
579	152	form_widget(form)	t
580	152	form_render(form)	f
581	152	form_fields(form)	f
582	152	render_form(form)	f
583	152	form_row(form)	f
584	153	label	f
585	153	input	t
586	153	error	f
587	153	label and input	f
588	153	error, label and input	f
589	154	Template	t
590	154	Form	f
591	154	Controller	f
592	155	HiddenType	t
593	155	SearchType	t
594	155	BirthdayType	t
595	155	PasswordType	t
596	155	SelectType	f
597	155	FloatType	f
598	155	BooleanType	f
599	156	set the validation_groups option to false	t
600	156	set the validation_enable option to false	f
601	156	set the enable_validation option to false	f
602	156	$this->createFormBuilder()->isValidated(false)	f
603	156	By using false as third parameter for createFormBuilder	f
604	157	Model data	f
605	157	Normalized data	t
606	157	Request data	f
607	157	View data	f
608	158	FormEvents::PRE_SET_DATA	f
609	158	FormEvents::SUBMIT	f
610	158	FormEvents::POST_SUBMIT	f
611	158	FormEvents::SET_DATA	t
612	159	$file->move(string $directory, string $name = null)	t
613	159	$file->upload(string $directory, string $name = null)	f
614	159	$file->uploadFile(string $directory, string $name = null)	f
615	159	$file->save(string $directory, string $name = null)	f
616	160	$builder->get('tags')->addModelTransformer(...);	t
617	160	$builder->add($builder->create('tags', TextType::class)->addModelTransformer(...));	t
618	160	$builder->add('tags', TextType::class)->addModelTransformer(...);	f
619	161	Control-Cache	t
620	161	Cache-Modifier	t
621	161	Expires	f
622	161	Last-Modified	f
623	161	Cache-Control	f
624	162	$response->mustRevalidate()	t
625	162	$response->isRevalidated()	f
626	162	$response->getRevalidated()	f
627	162	$response->mustBeRevalidated()	f
628	163	$response->isInformational()	t
629	163	$response->isSuccessful()	t
630	163	$response->isRedirection()	t
631	163	$response->isInvalid()	t
632	163	$response->isError()	f
633	164	$request->query->get('foo');	t
634	164	$request->request->get('foo');	f
635	164	{$request->query->all()}['foo'];	t
636	164	$request->get('foo');	t
637	165	$request->query->get('bar');	f
638	165	$request->request->get('bar');	t
639	165	{$request->query->all()}['baz'];	f
640	165	$request->post('bar');	f
641	166	return the value of the metadata lang	f
642	166	return an array of languages available in translations	f
643	166	return an array of languages the client accepts	t
644	166	does not exists	f
645	167	$request->headers->get('content_type');	t
646	167	$request->headers->get('content-type');	t
647	167	$request->headers->get('Content-Type');	t
648	167	$request->getContentType();	t
649	168	$request->isXmlHttpRequest();	t
650	168	$request->isAJAX();	f
651	168	$this->headers->get('X-Requested-With') === 'XMLHttpRequest';	t
652	169	JsonResponse	t
653	169	StreamResponse	f
654	169	BinaryFileResponse	t
655	169	XmlResponse	f
656	170	301	f
657	170	302	t
658	170	201	f
659	170	204	f
660	171	True	t
661	171	False	f
662	172	getPathInfo	t
663	172	getMethod	t
664	172	getLanguages	t
665	172	getHeaders	f
666	172	getHttpHost	t
667	172	getUrl	f
668	173	By you pass the value as second parameter of new Response()	t
669	173	By using setStatusCode()	t
670	173	By using setCodeStatus()	f
671	173	By using setHttpCode()	f
672	174	error404.html.twig	t
673	174	404.html.twig	f
674	174	twig.404.html.twig	f
675	175	web/assets/<bundle name>	f
676	175	web/assets/bundles/<bundle name>	f
677	175	web/bundles/<bundle name>	t
678	175	web/<bundle name>	f
679	176	mailer.transport	t
680	176	mailer.transporter	f
681	176	mail.transport	f
682	176	mail.transporter	f
683	177	framework.session.cookie_lifetime	t
684	177	framework.session.cookie.lifetime	f
685	177	framework.parameters.cookie_lifetime	f
686	177	framework.cookie_lifetime	f
687	178	Adapter	f
688	178	Decorator	f
689	178	Observer	f
690	178	Mediator	t
691	179	framework.templating.assets_version: v2	t
692	179	framework.templating.assets_version_number: v2	f
693	179	framework.twig.assets_version: v2	f
694	179	framework.twig.assets_version_number: v2	f
695	180	getEvents()	f
696	180	getSubscribedEvents()	t
697	180	getSubscribed()	f
698	180	getSubscribedEventsList()	f
699	181	dispatch()	t
700	181	send()	f
701	181	fire()	f
702	181	sendOff()	f
703	182	stopPropagation()	t
704	182	preventDefault()	f
705	182	stop()	f
706	182	off()	f
707	183	no	f
708	183	yes	t
709	183	yes, but not always	f
710	183	yes, but only once	f
711	184	trusted_proxies	t
712	184	proxies_trusted	f
713	184	proxies	f
714	184	proxies_list	f
715	185	%%s?%%s	t
716	185	%s?%s	f
717	185	?%%s	f
718	185	?%s	f
719	186	Symfony/Component/ClassLoader/MapClassLoader	t
720	186	Symfony/Component/ClassLoader/ClassMapLoader	f
721	186	Symfony/Component/ClassLoader/ClassLoader	f
722	186	Symfony/Component/ClassLoader/MapLoader	f
723	187	public function __construct($environment, $debug)	t
724	187	public function __construct($debug, $environment)	f
725	187	public function __construct($name, $environment, $debug)	f
726	187	public function __construct($environment, $debug, $name = null)	f
727	188	SYMFONY__	t
728	188	SF__	f
729	188	SYMFONY-	f
730	188	SF-	f
731	189	public function registerContainerConfiguration(LoaderInterface $loader)	t
732	189	public function registerConfigurationContainer(LoaderInterface $loader)	f
733	189	public function registerConfiguration(LoaderInterface $loader)	f
734	189	public function registerConfigurationPath(LoaderInterface $loader)	f
735	190	templates, app/	t
736	190	assets, web/	t
737	190	routes, app/routes.yml	f
738	190	translations, AppBundle/Resources/translations/	f
739	191	In Twig Bundle	f
740	191	In Twig Bridge	t
741	191	In FrameworkExtraBundle	f
742	192	True	f
743	192	False	t
744	193	GNU General Public License (GPL)	f
745	193	ISC License	f
746	193	BSD license	f
747	193	MIT license	t
748	194	kernel.request	t
749	194	kernel.controller	t
750	194	kernel.template	f
751	194	kernel.view	t
752	194	kernel.response	t
753	194	kernel.answer	f
754	194	kernel.finish_request	t
755	194	kernel.start_request	f
756	194	kernel.terminate	t
757	194	kernel.start	f
758	194	kernel.exception	t
759	195	yes	t
760	195	no	f
761	196	$event->isPropagationStopped()	t
762	196	$event->isStopped()	f
763	196	$event->isPropagationStop()	f
764	196	$event->isStop()	f
765	197	enable()	f
766	197	enableBundles()	f
767	197	register()	f
768	197	registerBundles()	t
769	198	__construct()	f
770	198	build()	t
771	198	boot()	f
772	198	bootstrap()	f
773	199	SensioFrameworkExtraBundle	t
774	199	DoctrineFixturesBundle	f
775	199	DoctrineMigrationBundle	f
776	199	SensioDoctrineBundle	f
777	200	Symfony\\Component\\Config\\Definition\\Builder\\TreeBuilder	t
778	200	Symfony\\Component\\Config\\Definition\\Builder\\HierarchyBuilder	f
779	200	Symfony\\Component\\Config\\Definition\\Builder\\NodeBuilder	f
780	200	Symfony\\Component\\Config\\Definition\\Builder\\Builder	f
781	201	scalar	t
782	201	array	t
783	201	enum	t
784	201	string	f
785	202	useAttributeAsKey()	t
786	202	isRequired()	t
787	202	setDefaultValue()	f
788	202	setValidation()	f
789	203	public function getParent()	t
790	203	public function getBundle()	f
791	203	public function getParentBundle()	f
792	203	public function getChild()	f
793	204	public function load(array $configs, ContainerBuilder $container)	t
794	204	public function load(array $config, ContainerBuilder $container)	f
795	204	public function load(ContainerBuilder $container, array $configs)	f
796	204	public function load(ContainerBuilder $container, array $config)	f
797	205	$container->getParameter('kernel.bundles')	t
798	205	$container->get('kernel')->getBundles()	t
799	205	$container->getParameter('bundles')	f
800	205	$container->getParameter('application.bundles')	f
801	206	True	t
802	206	False	f
803	207	AcmeNameBundle.php	t
804	207	README.md	t
805	207	LICENSE	t
806	207	Resources/doc/index.rst	t
807	207	Resources/config/routing.yml	f
808	207	Resources/config/services.yml	f
809	208	- { path: ^/admin, roles: ROLE_ADMIN }	t
810	208	- { path: ^/admin, acl: ROLE_ADMIN }	f
811	208	- { url: ^/admin, roles: ROLE_ADMIN }	f
812	208	- { url: ^/admin, acl: ROLE_ADMIN }	f
813	209	security.firewalls.<name>.form_login.use_referer	t
814	209	security.firewalls.<name>.form_login.after_login_referer	f
815	209	security.firewalls.<name>.form_login.referer	f
816	209	security.firewalls.<name>.form_login.success_referer	f
817	210	Symfony\\Bundle\\SecurityBundle\\Security\\FirewallContext services are publicly created that correspond to each context you create under your security.yml, a context is called an authenticator	f
818	210	Symfony\\Bundle\\SecurityBundle\\Security\\FirewallContext services are privately created that correspond to each context you create under your security.yml, a context is also known as per each firewall	f
819	210	Symfony\\Bundle\\SecurityBundle\\Security\\FirewallContext services are synthetically created that correspond to each context you create under your security.yml, each context is linked to an authenticator	f
820	210	Symfony\\Bundle\\SecurityBundle\\Security\\FirewallContext services are publicly created that correspond to each context you create under your security.yml, a context is related to each FirewallContext directly but is overall handled by a generic Symfony\\Bundle\\SecurityBundle\\Security\\Firewall object	t
821	211	Yes, it happens automatically	f
822	211	Yes, if firewalls have the same value of the `context` option	t
823	211	Yes, if option `shared` is set to true	f
824	211	No, it is not possible, firewalls are independent from each other	f
825	212	access_control: { path: ^/secure, requires_channel: https }	t
826	212	access_control: { path: ^/secure, use_https: true }	f
827	212	access_control: { path: ^/secure, always_use_https: true }	f
828	212	access_control: { path: ^/secure, schemes: [https] }	f
829	213	Encode user passwords using given algorithm	t
830	213	Encrypt a HTTP response	f
831	213	Encode all data in the application database	f
832	214	security.authentication.success	t
833	214	security.authentication.failure	t
834	214	security.interactive_login	t
835	214	security.login	f
836	214	security.switch_user	t
837	214	security.authentication.start	f
838	214	security.remember_me_login	f
839	215	Access is granted as soon as there is one voter granting access	t
840	215	Access is granted if there are more voters granting access than denying	f
841	215	Access is granted if all voters grant acces	f
842	215	Access is granted if no voters throw an exception	f
843	216	Access is granted as soon as there is one voter granting access	f
844	216	Access is granted if there are more voters granting access than denying	t
845	216	Access is granted if all voters grant acces	f
846	216	Access is granted if no voters throw an exception	f
847	217	Access is granted as soon as there is one voter granting access	f
848	217	Access is granted if there are more voters granting access than denying	f
849	217	Access is granted if all voters grant acces	t
850	217	Access is granted if no voters throw an exception	f
851	218	affirmative	t
852	218	consensus	t
853	218	unanimous	t
854	218	positive	f
855	218	negative	f
856	219	pattern: ^/(myurl)/	t
857	219	route: ^/(myurl)/	f
858	219	url: ^/(myurl)/	f
859	219	path: ^/(myurl)/	f
860	220	@Security("has_role("ROLE_ADMIN")")	t
861	220	@Security("is_granted("ROLE_ADMIN")")	f
862	220	@Security("restrict_for("ROLE_ADMIN")")	f
863	220	@Security("role("ROLE_ADMIN")")	f
864	221	IS_AUTHENTICATED_REMEMBERED	t
865	221	IS_AUTHENTICATED_FULLY	t
866	221	IS_AUTHENTICATED_ANONYMOUSLY	t
867	221	IS_NOT_AUTHENTICATED	f
868	222	$this->denyAccessUnlessGranted('ROLE_ADMIN')	t
869	222	@Security("has_role("ROLE_ADMIN")")	t
870	222	if (!$this->get('security.authorization_checker')->isGranted('ROLE_ADMIN')) { throw $this->createAccessDeniedException(); }	t
871	222	$this->user->denyAccessUnlessGranted('ROLE_ADMIN')	f
872	223	getRoles()	t
873	223	getPassword()	t
874	223	getSalt()	t
875	223	getUsername()	t
876	223	eraseCredentials()	t
877	223	getCredentials()	f
878	223	getHash()	f
879	223	getId()	f
880	224	loadUserByUsername($username)	t
881	224	loadUser($username)	f
882	224	refreshUser(UserInterface $user)	t
883	224	getUser($username)	f
884	224	supportsClass($class)	t
885	225	return $this->redirect($this->generateUrl('http://www.example.org'))	f
886	225	return $this->redirectUrl('http://www.example.org')	f
887	225	return $this->redirect('http://www.example.org')	t
888	225	return $this->generateUrl('http://www.example.org')	f
889	226	return $this->get('service.name')	t
890	226	return $this->getContainer('service.name')	f
891	226	return $this->getContainer()->get('service.name')	f
892	226	return $this->container->get('service.name')	t
893	227	Symfony\\Bundle\\FrameworkBundle\\Controller\\Controller	t
894	227	Symfony\\Component\\FrameworkBundle\\Controller\\Controller	f
895	227	Symfony\\Bundle\\HttpBundle\\Controller\\Controller	f
896	227	Symfony\\Component\\MvcBundle\\Controller\\Controller	f
897	228	@Cache	t
898	228	@ParamConverter	t
899	228	@Security	t
900	228	@Post	f
901	229	XmlResponse	t
902	229	JsonResponse	f
903	229	BinaryFileResponse	f
904	229	RedirectResponse	f
905	230	ParamConverter	t
906	230	ParameterConverter	f
907	230	Converter	f
908	230	ObjectConverter	f
909	231	FrameworkBundle:Controller:template	f
910	231	FrameworkBundle:Template:action	f
911	231	FrameworkBundle:Template:render	f
912	231	FrameworkBundle:Template:template	t
913	232	$_controller	t
914	232	$_route	t
915	232	$request	t
916	232	$_locale	t
917	232	$_format	t
918	233	@ParamConverter("post", options={"mapping": {"postSlug": "slug"}})	t
919	233	@ParamConverter("post", options={"mapping": {"slug": "postSlug"}})	f
920	233	@ParamConverter("post", options={"mapping": {"field": "slug"}})	f
921	233	@ParamConverter("post", options={"mapping": {"Post": "slug"}})	f
922	233	It is not possible, params and fields must match	f
\.


--
-- Data for Name: tbl_category; Type: TABLE DATA; Schema: public; Owner: -
--

COPY public.tbl_category (id, shortname, longname) FROM stdin;
1	Symfony	Symfony (all versions)
2	Symfony3	Symfony version 3
3	Symfony4	Symfony version 4
4	PHP Standards Recommendations	PHP Standards Recommendations
5	Dependency Injection	Dependency Injection
6	Templating	Templating
7	HTTP Cache	HTTP Cache
8	Routing	Routing
9	Miscellaneous	Miscellaneous
10	Standardization	Standardization
11	Validation	Validation
12	The Command Line	The Command Line
13	Automated tests	Automated tests
14	PHP	PHP
15	Forms	Forms
16	HTTP	HTTP
17	Architecture	Architecture
18	Bundles	Bundles
19	Security	Security
20	Controllers	Controllers
\.


--
-- Data for Name: tbl_history_answer; Type: TABLE DATA; Schema: public; Owner: -
--

COPY public.tbl_history_answer (id, question_history_id, date_time, answer_id, answer_text, answer_correct, correct_given, answer_succes) FROM stdin;
\.


--
-- Data for Name: tbl_history_question; Type: TABLE DATA; Schema: public; Owner: -
--

COPY public.tbl_history_question (id, workout_id, date_time, question_id, question_text, completed, question_success, duration) FROM stdin;
\.


--
-- Data for Name: tbl_question; Type: TABLE DATA; Schema: public; Owner: -
--

COPY public.tbl_question (id, text, created_at, updated_at) FROM stdin;
1	Psr-3 LoggerInterface exposes eight methods to write logs to the eight RFC 5424 levels, which level does not exist ?	2018-07-07 16:59:27	2018-07-07 16:59:27
2	Which methods is not in Psr-3	2018-07-07 16:59:27	2018-07-07 16:59:27
3	In Psr-3 what delimiter is used for placeholder names ?	2018-07-07 16:59:27	2018-07-07 16:59:27
4	Which Psr is about autoloading ?	2018-07-07 16:59:27	2018-07-07 16:59:27
5	According to Psr-2, Which code is correct ?	2018-07-07 16:59:27	2018-07-07 16:59:27
6	According to Psr-1 methods have to be declared like ?	2018-07-07 16:59:27	2018-07-07 16:59:27
7	Which Psr is about caching ?	2018-07-07 16:59:27	2018-07-07 16:59:27
8	Which Psr is deprecated now ?	2018-07-07 16:59:27	2018-07-07 16:59:27
9	Which line allows to inject the my.custom.service?	2018-07-07 16:59:27	2018-07-07 16:59:27
10	Which types of injection are available in the component?	2018-07-07 16:59:27	2018-07-07 16:59:27
11	Using XML, which of these lines are correct to inject a service?	2018-07-07 16:59:27	2018-07-07 16:59:27
12	Which of these methods are available in ContainerInterface?	2018-07-07 16:59:27	2018-07-07 16:59:27
13	Using YML, which line allows to inject proxy of the service?	2018-07-07 16:59:27	2018-07-07 16:59:27
14	Which method from CompilerPassInterface can modify the container here before it is dumped to PHP code?	2018-07-07 16:59:27	2018-07-07 16:59:27
15	Using Dependency Injection, what is the correct way to override a service class using ContainerBuilder class?	2018-07-07 16:59:27	2018-07-07 16:59:27
16	Using a compiler pass, how do you retrieve a definition of service ?	2018-07-07 16:59:27	2018-07-07 16:59:27
17	What are existing compiler pass order ?	2018-07-07 16:59:27	2018-07-07 16:59:27
18	As dev environment, what is the name of dumped container ?	2018-07-07 16:59:27	2018-07-07 16:59:27
19	True or false, parameters can also contain array values ?	2018-07-07 16:59:27	2018-07-07 16:59:27
20	What is the correct syntax to inject a service app.mailer ?	2018-07-07 16:59:27	2018-07-07 16:59:27
21	What is the correct syntax to inject a parameter mailer.transport ?	2018-07-07 16:59:27	2018-07-07 16:59:27
22	How to define a service as private ?	2018-07-07 16:59:27	2018-07-07 16:59:27
23	True or False, With default configuration each time you retrieve the service, you'll get the same instance ?	2018-07-07 16:59:27	2018-07-07 16:59:27
24	Using Twig, how to render flash messages of key "success" in a template?	2018-07-07 16:59:27	2018-07-07 16:59:27
25	Using Twig, how to render a text string in uppercase? ("test" must become "TEST")	2018-07-07 16:59:27	2018-07-07 16:59:27
26	Using Twig, syntax {% block example '' %} is equivalent to {% block example %}{% endblock %}	2018-07-07 16:59:27	2018-07-07 16:59:27
27	Using Twig, how to declare a variable in a template?	2018-07-07 16:59:27	2018-07-07 16:59:27
28	In class \\Twig_Extension, which method returns list of functions?	2018-07-07 16:59:27	2018-07-07 16:59:27
29	Using Twig, which comparison operator is not available?	2018-07-07 16:59:27	2018-07-07 16:59:27
30	Using Twig, what syntax used to execute statements?	2018-07-07 16:59:27	2018-07-07 16:59:27
31	Using Twig, how to check if current user has role "ROLE_TEST"?	2018-07-07 16:59:27	2018-07-07 16:59:27
32	Which Twig syntax allow to get the authenticated user name ?	2018-07-07 16:59:27	2018-07-07 16:59:27
33	If you want to overwrite the 404 page, what is the relative path of the template to create ?	2018-07-07 16:59:27	2018-07-07 16:59:27
34	In twig, what do you use if you want to optimize the size of the generated HTML content?	2018-07-07 16:59:27	2018-07-07 16:59:27
35	In Twig, which other Twig tag works as the old raw tag, for when you want to display code and not be parsed?	2018-07-07 16:59:27	2018-07-07 16:59:27
36	In Twig, the batch filter:	2018-07-07 16:59:27	2018-07-07 16:59:27
37	In Twig, when compiling a node tree how can you change the default compiler on your $twig object	2018-07-07 16:59:27	2018-07-07 16:59:27
38	In Twig which loader can you use to load templates from a database and the filesystem simultaneously?	2018-07-07 16:59:27	2018-07-07 16:59:27
39	in Twig, name which one is not a method of the Twig_Filter class:	2018-07-07 16:59:27	2018-07-07 16:59:27
40	In Twig, a visitor priority varies between:	2018-07-07 16:59:27	2018-07-07 16:59:27
41	In Twig, all these classes Escaper, Optimizer, SafeAnalysis, Sandbox share common methods :	2018-07-07 16:59:27	2018-07-07 16:59:27
42	In Twig this statement is valid:	2018-07-07 16:59:27	2018-07-07 16:59:27
43	in Twig, suppose you have an object person	2018-07-07 16:59:27	2018-07-07 16:59:27
44	In Twig:	2018-07-07 16:59:27	2018-07-07 16:59:27
45	There is a lesser known templating component with which you can build your own templating system not php and not twig.	2018-07-07 16:59:27	2018-07-07 16:59:27
46	In Symfony there is a lesser known templating component that is heavily used by Twig.	2018-07-07 16:59:27	2018-07-07 16:59:27
47	In Twig, how to render a controller asynchronously ?	2018-07-07 16:59:27	2018-07-07 16:59:27
48	True or false ? path() Twig function generates absolute urls.	2018-07-07 16:59:27	2018-07-07 16:59:27
49	Using Twig, what syntax used to print the content of a variable ?	2018-07-07 16:59:27	2018-07-07 16:59:27
50	Using Twig, what syntax used to add comments ?	2018-07-07 16:59:27	2018-07-07 16:59:27
51	What is the correct way to render a ESI tag using HTML?	2018-07-07 16:59:27	2018-07-07 16:59:27
52	What is the Twig function to render an ESI?	2018-07-07 16:59:27	2018-07-07 16:59:27
53	Which HTTP headers belongs to expiration cache model?	2018-07-07 16:59:27	2018-07-07 16:59:27
54	Which HTTP headers belongs to validation cache model?	2018-07-07 16:59:27	2018-07-07 16:59:27
55	Which HTTP status code must be returned if the cache is still valid ?	2018-07-07 16:59:27	2018-07-07 16:59:27
56	True or False ? ESI need to be activated in configuration.	2018-07-07 16:59:27	2018-07-07 16:59:27
57	According to HTTP/1.1 what is the max value for Expires ?	2018-07-07 16:59:27	2018-07-07 16:59:27
58	True or False ? You can use both validation and expiration within the same Response.	2018-07-07 16:59:27	2018-07-07 16:59:27
59	True or False ? Using ETag saves CPU cycles.	2018-07-07 16:59:27	2018-07-07 16:59:27
60	Using XML or YAML, how to declare a route for a specific domain/host?	2018-07-07 16:59:28	2018-07-07 16:59:28
61	What variable can be used as controller argument to get the name of the route name?	2018-07-07 16:59:28	2018-07-07 16:59:28
62	Using XML, how to ensure that a route is accessed via HTTPS?	2018-07-07 16:59:28	2018-07-07 16:59:28
63	Three special routing parameters are available in Symfony:	2018-07-07 16:59:28	2018-07-07 16:59:28
64	How to generate absolute URL for a given route in controller ?	2018-07-07 16:59:28	2018-07-07 16:59:28
65	Which controller/action allows to render a template without a specific controller?	2018-07-07 16:59:28	2018-07-07 16:59:28
66	What is the LoggerInterface correct namespace?	2018-07-07 16:59:28	2018-07-07 16:59:28
67	Using a dev environment, what is the correct cached file name of the dependency injection container?	2018-07-07 16:59:28	2018-07-07 16:59:28
68	Using a dev environment, what is the correct cached file name of the generated configuration routes?	2018-07-07 16:59:28	2018-07-07 16:59:28
69	Using Symfony\\Component\\Debug\\Debug static class, what is the only method available?	2018-07-07 16:59:28	2018-07-07 16:59:28
70	In order to be able to use render_hinclude(url(...)), we need to add this configuration in ``framework` section:	2018-07-07 16:59:28	2018-07-07 16:59:28
71	What is the correct CssSelector class namespace?	2018-07-07 16:59:28	2018-07-07 16:59:28
72	Event Listeners is use to Regrouping multiple listeners inside a single class ?	2018-07-07 16:59:28	2018-07-07 16:59:28
73	Using Console, what is the correct filename for a command?	2018-07-07 16:59:28	2018-07-07 16:59:28
74	How to render properly template located in src/Acme/TestBundle/Resources/views/Question/Item/list.html.twig?	2018-07-07 16:59:28	2018-07-07 16:59:28
75	Service naming conventions: select all valid services identifiers	2018-07-07 16:59:28	2018-07-07 16:59:28
76	In order to use validation group, which interface will you implement on your object?	2018-07-07 16:59:28	2018-07-07 16:59:28
77	Using Validator component, which method is used to validate a value against a constraint?	2018-07-07 16:59:28	2018-07-07 16:59:28
78	True or False ? All entities have at least 2 validation groups.	2018-07-07 16:59:28	2018-07-07 16:59:28
79	True or False ? We can also apply constraints on class getters with ``addPropertyConstraint()``	2018-07-07 16:59:28	2018-07-07 16:59:28
80	True or False ? In Symfony:	2018-07-07 16:59:28	2018-07-07 16:59:28
81	Which constraints exist?	2018-07-07 16:59:28	2018-07-07 16:59:28
82	Which annotation are valid ?	2018-07-07 16:59:28	2018-07-07 16:59:28
83	How to tell the validator to use a specific group ?	2018-07-07 16:59:28	2018-07-07 16:59:28
84	[SF3] What is the default signal sent by Process component to stop a process ?	2018-07-07 16:59:28	2018-07-07 16:59:28
85	[SF3] What can be placed in ????? to be valid?\n\n  use Symfony\\Component\\Yaml\\Yaml;\n\n  $value = Yaml::parse(?????);\n	2018-07-07 16:59:28	2018-07-07 16:59:28
86	Which helper is not available in the Console component?	2018-07-07 16:59:28	2018-07-07 16:59:28
87	Which event is not available in the Console Component?	2018-07-07 16:59:28	2018-07-07 16:59:28
88	How to launch Console application in interactive mode?	2018-07-07 16:59:28	2018-07-07 16:59:28
89	Using Console component, which of these events are available in Symfony\\Component\\Console\\ConsoleEvents?	2018-07-07 16:59:28	2018-07-07 16:59:28
90	Using Console component, which of these class allows to create custom output styles?	2018-07-07 16:59:28	2018-07-07 16:59:28
91	What is the console command to create a new bundle?	2018-07-07 16:59:28	2018-07-07 16:59:28
92	Which argument can be passed to the router:debug command?	2018-07-07 16:59:28	2018-07-07 16:59:28
93	What is the command to check the syntax of a Twig template?	2018-07-07 16:59:28	2018-07-07 16:59:28
94	What is the console command to clear cache?	2018-07-07 16:59:28	2018-07-07 16:59:28
95	Which tag name you should use when you register command as service?	2018-07-07 16:59:28	2018-07-07 16:59:28
96	What is the command line to list all known entities by doctrine 2 in your project ?	2018-07-07 16:59:28	2018-07-07 16:59:28
97	What is the command line to validate the doctrine mapping files ?	2018-07-07 16:59:28	2018-07-07 16:59:28
98	How do you display complete configuration of a bundle ?	2018-07-07 16:59:28	2018-07-07 16:59:28
99	Descriptors are objects to render documentation on Symfony Console Apps?	2018-07-07 16:59:28	2018-07-07 16:59:28
100	Does the Symfony Console component needs PHP globals internals variables to work?	2018-07-07 16:59:28	2018-07-07 16:59:28
101	What is the console command to create a new entity?	2018-07-07 16:59:28	2018-07-07 16:59:28
102	What is the command to update the database from entities?	2018-07-07 16:59:28	2018-07-07 16:59:28
103	What is the right command name to load Doctrine fixtures?	2018-07-07 16:59:28	2018-07-07 16:59:28
104	Symfony Console: which one(s) are valid verbosity levels ?	2018-07-07 16:59:28	2018-07-07 16:59:28
105	Symfony Console: InputArgument::REQUIRED, InputArgument::OPTIONAL and ... ?	2018-07-07 16:59:28	2018-07-07 16:59:28
106	Symfony Console: which of the following sentences are true?	2018-07-07 16:59:28	2018-07-07 16:59:28
107	Which function are mandatory to your command class ?	2018-07-07 16:59:28	2018-07-07 16:59:28
108	Which Question class are available ?	2018-07-07 16:59:28	2018-07-07 16:59:28
109	How to Call Other Commands in a command ?	2018-07-07 16:59:28	2018-07-07 16:59:28
110	Using PHPUnit, which method names are used to share test setup code?	2018-07-07 16:59:29	2018-07-07 16:59:29
111	How to disable constructor when mocking an object?	2018-07-07 16:59:29	2018-07-07 16:59:29
112	Which Symfony class offers method to test commands?	2018-07-07 16:59:29	2018-07-07 16:59:29
113	Using PHPUnit, which method allows to specify a mock response on second call?	2018-07-07 16:59:29	2018-07-07 16:59:29
114	Using PHPUnit, which method allows you to expect an exception to be thrown?	2018-07-07 16:59:29	2018-07-07 16:59:29
115	What command used for run all of your application tests by default?	2018-07-07 16:59:29	2018-07-07 16:59:29
116	Where live functional tests in Symfony (inside a bundle structure)?	2018-07-07 16:59:29	2018-07-07 16:59:29
117	Using Symfony\\Component\\BrowserKit\\Client which of these methods can be called?	2018-07-07 16:59:29	2018-07-07 16:59:29
118	Using Swiftmailer, which of these configuration allows to disable email delivery?	2018-07-07 16:59:29	2018-07-07 16:59:29
119	Using the Crawler client, how to follow a redirection ?	2018-07-07 16:59:29	2018-07-07 16:59:29
120	What is the short open tag for PHP?	2018-07-07 16:59:29	2018-07-07 16:59:29
121	Which of these operators is non-associative?	2018-07-07 16:59:29	2018-07-07 16:59:29
122	Since PHP 5.6+, if function foo() is defined in the namespace Myapp\\Utils\\Bar and your code is in namespace Myapp, what is the correct way to import the foo() function?	2018-07-07 16:59:29	2018-07-07 16:59:29
123	True or False? It is possible to import all classes from a namespace in PHP.	2018-07-07 16:59:29	2018-07-07 16:59:29
124	Which web services are supported natively in PHP?	2018-07-07 16:59:29	2018-07-07 16:59:29
125	Variable names and function names are, respectively:	2018-07-07 16:59:29	2018-07-07 16:59:29
126	Does PHP support function overloading?	2018-07-07 16:59:29	2018-07-07 16:59:29
127	Which of the following statements is not true?	2018-07-07 16:59:29	2018-07-07 16:59:29
128	Which of the following function declarations must be used to return a reference?	2018-07-07 16:59:29	2018-07-07 16:59:29
129	The ______ keyword is used to indicate an incomplete class or method, which must be further extended and/or implemented in order to be used.	2018-07-07 16:59:29	2018-07-07 16:59:29
130	According to the PHP Framework Interoperability Group, which PSRs concern best coding practices ?	2018-07-07 16:59:29	2018-07-07 16:59:29
131	Since PHP 5.4, which functionality allows horizontal composition of behavior ?	2018-07-07 16:59:29	2018-07-07 16:59:29
132	True or False ? A closure is a lambda function that is aware of its surrounding context.	2018-07-07 16:59:29	2018-07-07 16:59:29
133	True or False ? A lambda function is a named PHP function that can be stored in a variable.	2018-07-07 16:59:29	2018-07-07 16:59:29
134	Inside a form type, how to add a "date" field type in an input field?	2018-07-07 16:59:29	2018-07-07 16:59:29
135	Which method allows you to handle the request on a form instance?	2018-07-07 16:59:29	2018-07-07 16:59:29
136	From a Form instance, which method can you call to obtain a FormView instance?	2018-07-07 16:59:29	2018-07-07 16:59:29
137	In a Twig template, which function render a form field?	2018-07-07 16:59:29	2018-07-07 16:59:29
138	Using Form factory, how to define a CSRF provider?	2018-07-07 16:59:29	2018-07-07 16:59:29
139	Using Form component, which option can you use into setDefaultOptions() method to define CSRF field name?	2018-07-07 16:59:29	2018-07-07 16:59:29
140	Using Form component, option csrf_error_message can be use in setDefaultOptions() to define a custom CSRF error message?	2018-07-07 16:59:29	2018-07-07 16:59:29
141	Using Form component, which option will allow you to specify groups used for validation?	2018-07-07 16:59:29	2018-07-07 16:59:29
142	Which form event exist?	2018-07-07 16:59:29	2018-07-07 16:59:29
143	What data is inside FormEvent object at FormEvents::PRE_SET_DATA?	2018-07-07 16:59:29	2018-07-07 16:59:29
144	What data is inside FormEvent object at FormEvents::PRE_SUBMIT?	2018-07-07 16:59:29	2018-07-07 16:59:29
145	Which one of these types extends from text?	2018-07-07 16:59:29	2018-07-07 16:59:29
146	Which date type exist?	2018-07-07 16:59:29	2018-07-07 16:59:29
147	How do you bind a constraint to a form field?	2018-07-07 16:59:29	2018-07-07 16:59:29
148	Using form component, option "error_bubbling" will include error in current field.	2018-07-07 16:59:29	2018-07-07 16:59:29
149	How do you set default value?	2018-07-07 16:59:29	2018-07-07 16:59:29
150	Your Form class must extends ____ 	2018-07-07 16:59:29	2018-07-07 16:59:29
151	How to add an extra field `extra` with the form ?	2018-07-07 16:59:29	2018-07-07 16:59:29
152	How do you render all the form fields in twig ?	2018-07-07 16:59:29	2018-07-07 16:59:29
153	If you use form_widget() on a single fields, which parts are render ?	2018-07-07 16:59:29	2018-07-07 16:59:29
154	According to Symfony best practices, where you should add buttons ?	2018-07-07 16:59:29	2018-07-07 16:59:29
155	Which Field type exist?	2018-07-07 16:59:29	2018-07-07 16:59:29
156	How to Disable the Validation of Submitted Data	2018-07-07 16:59:29	2018-07-07 16:59:29
157	What data is inside FormEvent object at FormEvents::POST_SET_DATA?	2018-07-07 16:59:29	2018-07-07 16:59:29
158	Which form event dont exist?	2018-07-07 16:59:29	2018-07-07 16:59:29
159	How do you upload a UploadedFile ?	2018-07-07 16:59:29	2018-07-07 16:59:29
160	How to Use Data Transformers ?	2018-07-07 16:59:29	2018-07-07 16:59:29
161	Which of these HTTP headers does not exist?	2018-07-07 16:59:29	2018-07-07 16:59:29
162	Which one of these Response methods check if cache must be revalidated?	2018-07-07 16:59:29	2018-07-07 16:59:29
163	Using a Response instance, which of these methods are available to check status code?	2018-07-07 16:59:29	2018-07-07 16:59:29
164	How to access the `foo` GET parameter from Request object $request ?	2018-07-07 16:59:29	2018-07-07 16:59:29
165	How to access the `bar` POST parameter from Request object $request ?	2018-07-07 16:59:29	2018-07-07 16:59:29
166	The method getLanguages() from Request object:	2018-07-07 16:59:29	2018-07-07 16:59:29
167	How to get the Content Type from Request ?	2018-07-07 16:59:29	2018-07-07 16:59:29
168	How to check if a request has been sent using AJAX ?	2018-07-07 16:59:29	2018-07-07 16:59:29
169	Which Response subclasses are available?	2018-07-07 16:59:29	2018-07-07 16:59:29
170	Which HTTP status code should for a resource that moved temporarily ?	2018-07-07 16:59:29	2018-07-07 16:59:29
171	True or False ? Server returns an 403 HTTP status code when you are not allowed to access a resource	2018-07-07 16:59:29	2018-07-07 16:59:29
172	Which method exist in Symfony\\Component\\HttpFoundation\\Request ?	2018-07-07 16:59:29	2018-07-07 16:59:29
173	How can you set status code of Symfony\\Component\\HttpFoundation\\Response	2018-07-07 16:59:29	2018-07-07 16:59:29
174	To override the 404 error template for HTML page, how should you name the template ?	2018-07-07 16:59:29	2018-07-07 16:59:29
175	Where are publicly located bundles assets?	2018-07-07 16:59:29	2018-07-07 16:59:29
176	What is the Symfony mail service transport parameter name?	2018-07-07 16:59:29	2018-07-07 16:59:29
177	What is the path to set session cookie_lifetime parameter in a project configuration?	2018-07-07 16:59:29	2018-07-07 16:59:29
178	Which design pattern implements the EventDispatcher component?	2018-07-07 16:59:29	2018-07-07 16:59:29
179	What is the right parameter path to set a version number for assets?	2018-07-07 16:59:29	2018-07-07 16:59:29
180	Which method from EventSubscriberInterface return array of events that subscriber wants to listen to?	2018-07-07 16:59:30	2018-07-07 16:59:30
181	Which method from EventDispatcherInterface forwarding an event to all registered listeners?	2018-07-07 16:59:30	2018-07-07 16:59:30
182	Which method allows to prevent any other Event listeners from being called?	2018-07-07 16:59:30	2018-07-07 16:59:30
183	Is it possible to detect if an Event was stopped during runtime?	2018-07-07 16:59:30	2018-07-07 16:59:30
184	Using FrameworkBundle configuration, what is the correct path to fill proxies IP?	2018-07-07 16:59:30	2018-07-07 16:59:30
185	Using FrameworkBundle configuration, what is the default framework.templating.assets_version_format value?	2018-07-07 16:59:30	2018-07-07 16:59:30
186	Using ClassLoader component, which class can be used to autoload a static class map?	2018-07-07 16:59:30	2018-07-07 16:59:30
187	Instantiating a new Symfony\\Component\\HttpKernel\\Kernel, what is the correct arguments order?	2018-07-07 16:59:30	2018-07-07 16:59:30
188	What is the prefix of environment variables used by Symfony?	2018-07-07 16:59:30	2018-07-07 16:59:30
189	In Kernel class, which method do you need to redefine if you want to have multiple configuration files (config_dev.yml, config_test.yml, ...)?	2018-07-07 16:59:30	2018-07-07 16:59:30
190	According to Symfony best practices, ____ is/are located in _____ file/folder.	2018-07-07 16:59:30	2018-07-07 16:59:30
191	Where can you find the file `form_table_layout.html.twig` ?	2018-07-07 16:59:30	2018-07-07 16:59:30
192	If you pass 0 (zero) as third parameter (int $indent) in \\Symfony\\Component\\Yaml\\Yaml::dump, the generated YAML string will be indented with tabs	2018-07-07 16:59:30	2018-07-07 16:59:30
193	Symfony is released under which license ?	2018-07-07 16:59:30	2018-07-07 16:59:30
194	Which kernel event exist ?	2018-07-07 16:59:30	2018-07-07 16:59:30
195	Arguments are resolve before to Call Controller ?	2018-07-07 16:59:30	2018-07-07 16:59:30
196	How do you detect if an Event was stopped during runtime?	2018-07-07 16:59:30	2018-07-07 16:59:30
197	What is the method name in the Kernel class to enable bundles?	2018-07-07 16:59:30	2018-07-07 16:59:30
198	Which method is executed once when bundle is loaded after cache is cleared?	2018-07-07 16:59:30	2018-07-07 16:59:30
199	Which of these bundles comes with the Symfony Standard Edition?	2018-07-07 16:59:30	2018-07-07 16:59:30
200	Using Config component, which class is used to define hierarchy of configuration values?	2018-07-07 16:59:30	2018-07-07 16:59:30
201	Using Config component, which of these configuration node types are available?	2018-07-07 16:59:30	2018-07-07 16:59:30
202	Using Config component, which of these methods are existing?	2018-07-07 16:59:30	2018-07-07 16:59:30
203	Which method allows to override bundle in a bundle class?	2018-07-07 16:59:30	2018-07-07 16:59:30
204	What is the correct load() method definition in Symfony\\Component\\DependencyInjection\\Extension\\ExtensionInterface?	2018-07-07 16:59:30	2018-07-07 16:59:30
205	Using Symfony dependency injection container, how can you retrieve all bundles list?	2018-07-07 16:59:30	2018-07-07 16:59:30
206	True or False ? the entry point of a Bundle MUST implement `Symfony\\Component\\HttpKernel\\Bundle\\BundleInterface` interface.	2018-07-07 16:59:30	2018-07-07 16:59:30
207	According to Symfony best practices, which files files are mandatory in your bundle AcmeNameBundle directory Structure ?	2018-07-07 16:59:30	2018-07-07 16:59:30
208	Which line is correct to add a security.access_control line?	2018-07-07 16:59:30	2018-07-07 16:59:30
209	After a login success, what is the parameter name to redirect on referer URL?	2018-07-07 16:59:30	2018-07-07 16:59:30
210	In Symfony Security component:	2018-07-07 16:59:30	2018-07-07 16:59:30
211	Is user authenticated in all of security firewalls after a successful login:	2018-07-07 16:59:30	2018-07-07 16:59:30
212	How to force a secure area to use the HTTPS protocol in the security config?	2018-07-07 16:59:30	2018-07-07 16:59:30
213	What is the purpose of security encoders in security.yml?	2018-07-07 16:59:30	2018-07-07 16:59:30
214	Which authentication events exist in the Security component?	2018-07-07 16:59:30	2018-07-07 16:59:30
215	What does the default strategy `affirmative` of the access decision manager mean?	2018-07-07 16:59:30	2018-07-07 16:59:30
216	What does the strategy `consensus` of the access decision manager mean?	2018-07-07 16:59:30	2018-07-07 16:59:30
217	What does the strategy `unanimous` of the access decision manager mean?	2018-07-07 16:59:30	2018-07-07 16:59:30
218	What decision strategy exist ?	2018-07-07 16:59:30	2018-07-07 16:59:30
219	How to Restrict Firewalls to a Specific Request Pattern ?	2018-07-07 16:59:30	2018-07-07 16:59:30
220	Which annotation is valid to check role ?	2018-07-07 16:59:30	2018-07-07 16:59:30
221	Which default role exist ?	2018-07-07 16:59:30	2018-07-07 16:59:30
222	How can you deny access to user in your controller ?	2018-07-07 16:59:30	2018-07-07 16:59:30
223	For implements Symfony\\Component\\Security\\Core\\User\\UserInterface which method you have to define ?	2018-07-07 16:59:30	2018-07-07 16:59:30
224	For implements Symfony\\Component\\Security\\Core\\User\\UserProviderInterface which method you have to define ?	2018-07-07 16:59:30	2018-07-07 16:59:30
225	How to perform a redirection on example.org in a controller?	2018-07-07 16:59:30	2018-07-07 16:59:30
226	Which method(s) can be used to retrieve a service in a controller?	2018-07-07 16:59:30	2018-07-07 16:59:30
227	Which class may be extended by your controllers?	2018-07-07 16:59:30	2018-07-07 16:59:30
228	Which of these annotations can be used in a controller?	2018-07-07 16:59:30	2018-07-07 16:59:30
229	Which of these response objects does not exists?	2018-07-07 16:59:30	2018-07-07 16:59:30
230	Which class will you use to convert an action parameter?	2018-07-07 16:59:30	2018-07-07 16:59:30
231	Which controller/action allows to render a template without a specific controller?	2018-07-07 16:59:30	2018-07-07 16:59:30
232	Which of these variables are available in Controllers?	2018-07-07 16:59:30	2018-07-07 16:59:30
233	According to this action :\n\n/**\n * @Route("/comment/{postSlug}/new", name = "comment_new")\n *\n */\npublic function newAction(Request $request, Post $post)\n{\n    // ...\n}\n\n\nHow my ParamConverter should be configured to match "postSlug" params with "slug" in Post ?\n	2018-07-07 16:59:30	2018-07-07 16:59:30
\.


--
-- Data for Name: tbl_queston_category; Type: TABLE DATA; Schema: public; Owner: -
--

COPY public.tbl_queston_category (question_id, category_id) FROM stdin;
1	4
1	2
2	4
2	2
3	4
3	2
4	4
4	2
5	4
5	2
6	4
6	2
7	4
7	2
8	4
8	2
9	5
9	2
10	5
10	2
11	5
11	2
12	5
12	2
13	5
13	2
14	5
14	2
15	5
15	2
16	5
16	2
17	5
17	2
18	5
18	2
19	5
19	2
20	5
20	2
21	5
21	2
22	5
22	2
23	5
23	2
24	6
24	2
25	6
25	2
26	6
26	2
27	6
27	2
28	6
28	2
29	6
29	2
30	6
30	2
31	6
31	2
32	6
32	2
33	6
33	2
34	6
34	2
35	6
35	2
36	6
36	2
37	6
37	2
38	6
38	2
39	6
39	2
40	6
40	2
41	6
41	2
42	6
42	2
43	6
43	2
44	6
44	2
45	6
45	2
46	6
46	2
47	6
47	2
48	6
48	2
49	6
49	2
50	6
50	2
51	7
51	2
52	7
52	2
53	7
53	2
54	7
54	2
55	7
55	2
56	7
56	2
57	7
57	2
58	7
58	2
59	7
59	2
60	8
60	2
61	8
61	2
62	8
62	2
63	8
63	2
64	8
64	2
65	9
65	2
66	9
66	2
67	9
67	2
68	9
68	2
69	9
69	2
70	9
70	2
71	9
71	2
72	9
72	2
73	10
73	2
74	10
74	2
75	10
75	2
76	11
76	2
77	11
77	2
78	11
78	2
79	11
79	2
80	11
80	2
81	11
81	2
82	11
82	2
83	11
83	2
84	2
85	2
86	12
86	2
87	12
87	2
88	12
88	2
89	12
89	2
90	12
90	2
91	12
91	2
92	12
92	2
93	12
93	2
94	12
94	2
95	12
95	2
96	12
96	2
97	12
97	2
98	12
98	2
99	12
99	2
100	12
100	2
101	12
101	2
102	12
102	2
103	12
103	2
104	12
104	2
105	12
105	2
106	12
106	2
107	12
107	2
108	12
108	2
109	12
109	2
110	13
110	2
111	13
111	2
112	13
112	2
113	13
113	2
114	13
114	2
115	13
115	2
116	13
116	2
117	13
117	2
118	13
118	2
119	13
119	2
120	14
120	2
121	14
121	2
122	14
122	2
123	14
123	2
124	14
124	2
125	14
125	2
126	14
126	2
127	14
127	2
128	14
128	2
129	14
129	2
130	14
130	2
131	14
131	2
132	14
132	2
133	14
133	2
134	15
134	2
135	15
135	2
136	15
136	2
137	15
137	2
138	15
138	2
139	15
139	2
140	15
140	2
141	15
141	2
142	15
142	2
143	15
143	2
144	15
144	2
145	15
145	2
146	15
146	2
147	15
147	2
148	15
148	2
149	15
149	2
150	15
150	2
151	15
151	2
152	15
152	2
153	15
153	2
154	15
154	2
155	15
155	2
156	15
156	2
157	15
157	2
158	15
158	2
159	15
159	2
160	15
160	2
161	16
161	2
162	16
162	2
163	16
163	2
164	16
164	2
165	16
165	2
166	16
166	2
167	16
167	2
168	16
168	2
169	16
169	2
170	16
170	2
171	16
171	2
172	16
172	2
173	16
173	2
174	16
174	2
175	17
175	2
176	17
176	2
177	17
177	2
178	17
178	2
179	17
179	2
180	17
180	2
181	17
181	2
182	17
182	2
183	17
183	2
184	17
184	2
185	17
185	2
186	17
186	2
187	17
187	2
188	17
188	2
189	17
189	2
190	17
190	2
191	17
191	2
192	17
192	2
193	17
193	2
194	17
194	2
195	17
195	2
196	17
196	2
197	18
197	2
198	18
198	2
199	18
199	2
200	18
200	2
201	18
201	2
202	18
202	2
203	18
203	2
204	18
204	2
205	18
205	2
206	18
206	2
207	18
207	2
208	19
208	2
209	19
209	2
210	19
210	2
211	19
211	2
212	19
212	2
213	19
213	2
214	19
214	2
215	19
215	2
216	19
216	2
217	19
217	2
218	19
218	2
219	19
219	2
220	19
220	2
221	19
221	2
222	19
222	2
223	19
223	2
224	19
224	2
225	20
225	2
226	20
226	2
227	20
227	2
228	20
228	2
229	20
229	2
230	20
230	2
231	20
231	2
232	20
232	2
233	20
233	2
\.


--
-- Data for Name: tbl_quiz; Type: TABLE DATA; Schema: public; Owner: -
--

COPY public.tbl_quiz (id, title, summary, number_of_questions, active, created_at, updated_at) FROM stdin;
1	Symfony 3	Questions about all themes of Symfony 3	10	t	2018-07-07 17:01:07	2018-07-07 17:01:07
\.


--
-- Data for Name: tbl_quiz_category; Type: TABLE DATA; Schema: public; Owner: -
--

COPY public.tbl_quiz_category (quiz_id, category_id) FROM stdin;
1	2
\.


--
-- Data for Name: tbl_user; Type: TABLE DATA; Schema: public; Owner: -
--

COPY public.tbl_user (id, username, email, password, is_active, roles) FROM stdin;
1	superadmin	superadmin@domain.tld	$2y$13$wazk0TN/F5PSSf7/sobVl.vsy3XQHsvdqj8Jzw7oGji7Z.va0Te.e	t	a:2:{i:0;s:16:"ROLE_SUPER_ADMIN";i:1;s:9:"ROLE_USER";}
2	admin	admin@domain.tld	$2y$13$hB1uPxOCzbdiP0A8e.MosuDI7n8lsKprz9NdZ8.j7B/5RHv1o6UI6	t	a:2:{i:0;s:10:"ROLE_ADMIN";i:1;s:9:"ROLE_USER";}
3	teacher	teacher@domain.tld	$2y$13$8NHoqDTrHTUH4pIxwLrJTOuGSbpVR479JuE/a2SvTDZOEdHiWjgs6	t	a:2:{i:0;s:12:"ROLE_TEACHER";i:1;s:9:"ROLE_USER";}
4	user	user@domain.tld	$2y$13$9Rz8ft0axtQyCqHFjJSkgO6h.0ooGRf31HKSj5PTrZb2C8P0xngke	t	a:1:{i:0;s:9:"ROLE_USER";}
\.


--
-- Data for Name: tbl_workout; Type: TABLE DATA; Schema: public; Owner: -
--

COPY public.tbl_workout (id, student_id, quiz_id, started_at, ended_at, number_of_questions, completed) FROM stdin;
\.


--
-- Name: tbl_answer_id_seq; Type: SEQUENCE SET; Schema: public; Owner: -
--

SELECT pg_catalog.setval('public.tbl_answer_id_seq', 922, true);


--
-- Name: tbl_category_id_seq; Type: SEQUENCE SET; Schema: public; Owner: -
--

SELECT pg_catalog.setval('public.tbl_category_id_seq', 20, true);


--
-- Name: tbl_history_answer_id_seq; Type: SEQUENCE SET; Schema: public; Owner: -
--

SELECT pg_catalog.setval('public.tbl_history_answer_id_seq', 1, false);


--
-- Name: tbl_history_question_id_seq; Type: SEQUENCE SET; Schema: public; Owner: -
--

SELECT pg_catalog.setval('public.tbl_history_question_id_seq', 1, false);


--
-- Name: tbl_question_id_seq; Type: SEQUENCE SET; Schema: public; Owner: -
--

SELECT pg_catalog.setval('public.tbl_question_id_seq', 233, true);


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

SELECT pg_catalog.setval('public.tbl_workout_id_seq', 1, false);


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
-- Name: tbl_question tbl_question_pkey; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.tbl_question
    ADD CONSTRAINT tbl_question_pkey PRIMARY KEY (id);


--
-- Name: tbl_queston_category tbl_queston_category_pkey; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.tbl_queston_category
    ADD CONSTRAINT tbl_queston_category_pkey PRIMARY KEY (question_id, category_id);


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
-- Name: idx_483bda3f12469de2; Type: INDEX; Schema: public; Owner: -
--

CREATE INDEX idx_483bda3f12469de2 ON public.tbl_queston_category USING btree (category_id);


--
-- Name: idx_483bda3f1e27f6bf; Type: INDEX; Schema: public; Owner: -
--

CREATE INDEX idx_483bda3f1e27f6bf ON public.tbl_queston_category USING btree (question_id);


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
-- Name: tbl_queston_category fk_483bda3f12469de2; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.tbl_queston_category
    ADD CONSTRAINT fk_483bda3f12469de2 FOREIGN KEY (category_id) REFERENCES public.tbl_category(id) ON DELETE CASCADE;


--
-- Name: tbl_queston_category fk_483bda3f1e27f6bf; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.tbl_queston_category
    ADD CONSTRAINT fk_483bda3f1e27f6bf FOREIGN KEY (question_id) REFERENCES public.tbl_question(id) ON DELETE CASCADE;


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
-- Name: tbl_history_question fk_fcd2a776a6cccfc9; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.tbl_history_question
    ADD CONSTRAINT fk_fcd2a776a6cccfc9 FOREIGN KEY (workout_id) REFERENCES public.tbl_workout(id);


--
-- PostgreSQL database dump complete
--

