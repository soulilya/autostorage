--
-- PostgreSQL database dump
--

-- Dumped from database version 12.3
-- Dumped by pg_dump version 12.4 (Ubuntu 12.4-1.pgdg18.04+1)

-- Started on 2020-08-14 13:26:42 MSK

SET statement_timeout = 0;
SET lock_timeout = 0;
SET idle_in_transaction_session_timeout = 0;
SET client_encoding = 'UTF8';
SET standard_conforming_strings = on;
SELECT pg_catalog.set_config('search_path', '', false);
SET check_function_bodies = false;
SET xmloption = content;
SET client_min_messages = warning;
SET row_security = off;

--
-- TOC entry 3 (class 2615 OID 2200)
-- Name: public; Type: SCHEMA; Schema: -; Owner: postgres
--

CREATE SCHEMA public;


ALTER SCHEMA public OWNER TO postgres;

--
-- TOC entry 2255 (class 0 OID 0)
-- Dependencies: 3
-- Name: SCHEMA public; Type: COMMENT; Schema: -; Owner: postgres
--

COMMENT ON SCHEMA public IS 'standard public schema';


SET default_tablespace = '';

SET default_table_access_method = heap;

--
-- TOC entry 208 (class 1259 OID 16677)
-- Name: cars; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.cars (
    id integer NOT NULL,
    manufacturer character varying(80) NOT NULL,
    model character varying(30) NOT NULL,
    produced integer,
    kit character varying(30),
    specifications text
);


ALTER TABLE public.cars OWNER TO postgres;

--
-- TOC entry 207 (class 1259 OID 16675)
-- Name: cars_id_seq; Type: SEQUENCE; Schema: public; Owner: postgres
--

CREATE SEQUENCE public.cars_id_seq
    AS integer
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE public.cars_id_seq OWNER TO postgres;

--
-- TOC entry 2256 (class 0 OID 0)
-- Dependencies: 207
-- Name: cars_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: postgres
--

ALTER SEQUENCE public.cars_id_seq OWNED BY public.cars.id;


--
-- TOC entry 202 (class 1259 OID 16428)
-- Name: cars_pk_seq; Type: SEQUENCE; Schema: public; Owner: postgres
--

CREATE SEQUENCE public.cars_pk_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE public.cars_pk_seq OWNER TO postgres;

--
-- TOC entry 210 (class 1259 OID 16688)
-- Name: storage; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.storage (
    id integer NOT NULL,
    car_id integer NOT NULL,
    status character varying(20) NOT NULL,
    qty integer
);


ALTER TABLE public.storage OWNER TO postgres;

--
-- TOC entry 209 (class 1259 OID 16686)
-- Name: storage_id_seq; Type: SEQUENCE; Schema: public; Owner: postgres
--

CREATE SEQUENCE public.storage_id_seq
    AS integer
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE public.storage_id_seq OWNER TO postgres;

--
-- TOC entry 2257 (class 0 OID 0)
-- Dependencies: 209
-- Name: storage_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: postgres
--

ALTER SEQUENCE public.storage_id_seq OWNED BY public.storage.id;


--
-- TOC entry 203 (class 1259 OID 16435)
-- Name: storage_pk_seq; Type: SEQUENCE; Schema: public; Owner: postgres
--

CREATE SEQUENCE public.storage_pk_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE public.storage_pk_seq OWNER TO postgres;

--
-- TOC entry 206 (class 1259 OID 16667)
-- Name: users; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.users (
    id integer NOT NULL,
    login character varying(20) NOT NULL,
    pass character varying(150) NOT NULL,
    role character varying(20) NOT NULL
);


ALTER TABLE public.users OWNER TO postgres;

--
-- TOC entry 205 (class 1259 OID 16665)
-- Name: users_id_seq; Type: SEQUENCE; Schema: public; Owner: postgres
--

CREATE SEQUENCE public.users_id_seq
    AS integer
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE public.users_id_seq OWNER TO postgres;

--
-- TOC entry 2258 (class 0 OID 0)
-- Dependencies: 205
-- Name: users_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: postgres
--

ALTER SEQUENCE public.users_id_seq OWNED BY public.users.id;


--
-- TOC entry 204 (class 1259 OID 16441)
-- Name: users_pk_seq; Type: SEQUENCE; Schema: public; Owner: postgres
--

CREATE SEQUENCE public.users_pk_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE public.users_pk_seq OWNER TO postgres;

--
-- TOC entry 2102 (class 2604 OID 16680)
-- Name: cars id; Type: DEFAULT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.cars ALTER COLUMN id SET DEFAULT nextval('public.cars_id_seq'::regclass);


--
-- TOC entry 2103 (class 2604 OID 16691)
-- Name: storage id; Type: DEFAULT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.storage ALTER COLUMN id SET DEFAULT nextval('public.storage_id_seq'::regclass);


--
-- TOC entry 2101 (class 2604 OID 16670)
-- Name: users id; Type: DEFAULT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.users ALTER COLUMN id SET DEFAULT nextval('public.users_id_seq'::regclass);


--
-- TOC entry 2247 (class 0 OID 16677)
-- Dependencies: 208
-- Data for Name: cars; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY public.cars (id, manufacturer, model, produced, kit, specifications) FROM stdin;
\.


--
-- TOC entry 2249 (class 0 OID 16688)
-- Dependencies: 210
-- Data for Name: storage; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY public.storage (id, car_id, status, qty) FROM stdin;
\.


--
-- TOC entry 2245 (class 0 OID 16667)
-- Dependencies: 206
-- Data for Name: users; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY public.users (id, login, pass, role) FROM stdin;
1	admin	$2y$10$UW0Ad5P8DIxGYf32vjpKYetl5Gyt9IwoCZ298jmgQ5EhO7u.XOYtG	Администратор
2	manager	$2y$10$LdfuH59CnPHHUkuknpZWe.X1V5M7g7OUsn2Bqw9PhxVr5A/8TPqrq	Менеджер
3	storekeeper	$2y$10$925OjX9UuvDv9xO0Xk6kCevV8t/LeAvIxDRgnz6oMEeMXXIkvez7u	Кладовщик
\.


--
-- TOC entry 2259 (class 0 OID 0)
-- Dependencies: 207
-- Name: cars_id_seq; Type: SEQUENCE SET; Schema: public; Owner: postgres
--

SELECT pg_catalog.setval('public.cars_id_seq', 1, false);


--
-- TOC entry 2260 (class 0 OID 0)
-- Dependencies: 202
-- Name: cars_pk_seq; Type: SEQUENCE SET; Schema: public; Owner: postgres
--

SELECT pg_catalog.setval('public.cars_pk_seq', 21, true);


--
-- TOC entry 2261 (class 0 OID 0)
-- Dependencies: 209
-- Name: storage_id_seq; Type: SEQUENCE SET; Schema: public; Owner: postgres
--

SELECT pg_catalog.setval('public.storage_id_seq', 1, false);


--
-- TOC entry 2262 (class 0 OID 0)
-- Dependencies: 203
-- Name: storage_pk_seq; Type: SEQUENCE SET; Schema: public; Owner: postgres
--

SELECT pg_catalog.setval('public.storage_pk_seq', 21, true);


--
-- TOC entry 2263 (class 0 OID 0)
-- Dependencies: 205
-- Name: users_id_seq; Type: SEQUENCE SET; Schema: public; Owner: postgres
--

SELECT pg_catalog.setval('public.users_id_seq', 3, true);


--
-- TOC entry 2264 (class 0 OID 0)
-- Dependencies: 204
-- Name: users_pk_seq; Type: SEQUENCE SET; Schema: public; Owner: postgres
--

SELECT pg_catalog.setval('public.users_pk_seq', 59, true);


--
-- TOC entry 2109 (class 2606 OID 16685)
-- Name: cars cars_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.cars
    ADD CONSTRAINT cars_pkey PRIMARY KEY (id);


--
-- TOC entry 2111 (class 2606 OID 16700)
-- Name: storage storage_car_un; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.storage
    ADD CONSTRAINT storage_car_un UNIQUE (car_id);


--
-- TOC entry 2113 (class 2606 OID 16693)
-- Name: storage storage_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.storage
    ADD CONSTRAINT storage_pkey PRIMARY KEY (id);


--
-- TOC entry 2105 (class 2606 OID 16672)
-- Name: users users_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.users
    ADD CONSTRAINT users_pkey PRIMARY KEY (id);


--
-- TOC entry 2107 (class 2606 OID 16674)
-- Name: users users_un; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.users
    ADD CONSTRAINT users_un UNIQUE (login);


--
-- TOC entry 2114 (class 2606 OID 16694)
-- Name: storage storage_car_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.storage
    ADD CONSTRAINT storage_car_id_fkey FOREIGN KEY (car_id) REFERENCES public.cars(id);


-- Completed on 2020-08-14 13:26:43 MSK

--
-- PostgreSQL database dump complete
--

