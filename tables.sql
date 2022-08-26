-- Table: public.costumers


CREATE TABLE IF NOT EXISTS public.costumers
(
    id serial NOT NULL,
    name character varying(255) COLLATE pg_catalog."default" NOT NULL,
    cpf character varying(255) COLLATE pg_catalog."default" NOT NULL,
    rg character varying(255) COLLATE pg_catalog."default" NOT NULL,
    civil_state character varying(255) COLLATE pg_catalog."default",
    spouse character varying(255) COLLATE pg_catalog."default",
    filiation character varying(255) COLLATE pg_catalog."default",
    address character varying(255) COLLATE pg_catalog."default",
    cellphone_number character varying(40) COLLATE pg_catalog."default",
    birthday date,
    status character varying(40) COLLATE pg_catalog."default" DEFAULT 'Ativo'::character varying,
    CONSTRAINT costumers_pkey PRIMARY KEY (id)
)

TABLESPACE pg_default;

ALTER TABLE IF EXISTS public.costumers
    OWNER to postgres;

-- Table: public.payment_method

-- DROP TABLE IF EXISTS public.payment_method;

CREATE TABLE IF NOT EXISTS public.payment_method
(
    id integer NOT NULL,
    description character varying(255) COLLATE pg_catalog."default",
    CONSTRAINT payment_method_pkey PRIMARY KEY (id)
)

TABLESPACE pg_default;

ALTER TABLE IF EXISTS public.payment_method
    OWNER to postgres;

-- Table: public.payments


CREATE TABLE IF NOT EXISTS public.payments
(
    id integer NOT NULL DEFAULT nextval('payments_id_seq'::regclass),
    id_sale integer,
    payment_method integer,
    value numeric,
    date date,
    payer character varying(255) COLLATE pg_catalog."default",
    CONSTRAINT payments_pkey PRIMARY KEY (id),
    CONSTRAINT payments_id_sale_fkey FOREIGN KEY (id_sale)
        REFERENCES public.sales (id) MATCH SIMPLE
        ON UPDATE NO ACTION
        ON DELETE CASCADE,
    CONSTRAINT payments_payment_method_fkey FOREIGN KEY (payment_method)
        REFERENCES public.payment_method (id) MATCH SIMPLE
        ON UPDATE NO ACTION
        ON DELETE NO ACTION
)

TABLESPACE pg_default;

ALTER TABLE IF EXISTS public.payments
    OWNER to postgres;


-- Table: public.products

-- DROP TABLE IF EXISTS public.products;

CREATE TABLE IF NOT EXISTS public.products
(
    id integer NOT NULL,
    description character varying(255) COLLATE pg_catalog."default",
    brand character varying(255) COLLATE pg_catalog."default",
    type integer,
    size character(2) COLLATE pg_catalog."default",
    color character varying(255) COLLATE pg_catalog."default",
    price numeric,
    quantity integer,
    CONSTRAINT products_pkey PRIMARY KEY (id)
)

TABLESPACE pg_default;

ALTER TABLE IF EXISTS public.products
    OWNER to postgres;

-- Table: public.sales

-- DROP TABLE IF EXISTS public.sales;

CREATE TABLE IF NOT EXISTS public.sales
(
    id integer NOT NULL DEFAULT nextval('sales_id_seq'::regclass),
    id_costumer integer,
    payment_method integer,
    status character varying(10) COLLATE pg_catalog."default",
    date date DEFAULT now(),
    total_amount numeric,
    leftforpay numeric,
    CONSTRAINT sales_pkey PRIMARY KEY (id),
    CONSTRAINT sales_id_costumer_fkey FOREIGN KEY (id_costumer)
        REFERENCES public.costumers (id) MATCH SIMPLE
        ON UPDATE NO ACTION
        ON DELETE NO ACTION
)

TABLESPACE pg_default;

ALTER TABLE IF EXISTS public.sales
    OWNER to postgres;

-- Table: public.sales_product

-- DROP TABLE IF EXISTS public.sales_product;

CREATE TABLE IF NOT EXISTS public.sales_product
(
    id integer NOT NULL DEFAULT nextval('sales_product_id_seq'::regclass),
    id_sale integer,
    id_product integer,
    quantity integer,
    discount numeric,
    total_amount numeric,
    price numeric,
    CONSTRAINT sales_product_pkey PRIMARY KEY (id),
    CONSTRAINT sales_product_id_product_fkey FOREIGN KEY (id_product)
        REFERENCES public.products (id) MATCH SIMPLE
        ON UPDATE NO ACTION
        ON DELETE NO ACTION,
    CONSTRAINT sales_product_id_sale_fkey FOREIGN KEY (id_sale)
        REFERENCES public.sales (id) MATCH SIMPLE
        ON UPDATE NO ACTION
        ON DELETE NO ACTION
)

TABLESPACE pg_default;

ALTER TABLE IF EXISTS public.sales_product
    OWNER to postgres;

-- Table: public.users

-- DROP TABLE IF EXISTS public.users;

CREATE TABLE IF NOT EXISTS public.users
(
    id integer NOT NULL DEFAULT nextval('users_id_seq'::regclass),
    name character varying(255) COLLATE pg_catalog."default" NOT NULL,
    cpf character varying(255) COLLATE pg_catalog."default" NOT NULL,
    email character varying(255) COLLATE pg_catalog."default" NOT NULL,
    password character varying(255) COLLATE pg_catalog."default",
    permission_level integer DEFAULT 1,
    CONSTRAINT users_pkey PRIMARY KEY (id),
    CONSTRAINT fk_permission_level FOREIGN KEY (permission_level)
        REFERENCES public.users_permissions (id) MATCH SIMPLE
        ON UPDATE NO ACTION
        ON DELETE NO ACTION
)

TABLESPACE pg_default;

ALTER TABLE IF EXISTS public.users
    OWNER to postgres;

    -- Table: public.users_permissions

-- DROP TABLE IF EXISTS public.users_permissions;

CREATE TABLE IF NOT EXISTS public.users_permissions
(
    id integer NOT NULL DEFAULT nextval('users_permissions_id_seq'::regclass),
    description character varying(255) COLLATE pg_catalog."default",
    CONSTRAINT users_permissions_pkey PRIMARY KEY (id)
)

TABLESPACE pg_default;

ALTER TABLE IF EXISTS public.users_permissions
    OWNER to postgres;

insert into payment_method(1, "Vestuário");
insert into payment_method(2, "Acessório");
insert into payment_method(3, "Cosmético");

-- View: public.all_products_info

-- DROP VIEW public.all_products_info;

CREATE OR REPLACE VIEW public.all_products_info
 AS
 SELECT p.id,
    p.description,
    p.brand,
    p.type,
    p.size,
    p.color,
    p.price,
    p.quantity,
    sp.id AS id_sp,
    sp.quantity AS quantity_sp,
    sp.discount AS discount_sp,
    sp.total_amount AS total_amount_sp
   FROM products p
     JOIN sales_product sp ON p.id = sp.id_product;

ALTER TABLE public.all_products_info
    OWNER TO postgres;

-- View: public.sales_info

-- DROP VIEW public.sales_info;

CREATE OR REPLACE VIEW public.sales_info
 AS
 SELECT s.id AS sale_id,
    cos.name AS costumer_name,
    cos.id AS id_costumer,
    s.payment_method,
    pm.description AS payment_description,
    s.status,
    s.date,
    s.total_amount,
    s.leftforpay AS left_for_pay,
    string_agg(((((p.id || ' - '::text) || p.description::text) || ' ('::text) || sp.quantity) || ')'::text, ','::text) AS products,
    string_agg(to_char(sp.id_product, '9'::text), ','::text) AS products_ids
   FROM sales s
     JOIN costumers cos ON cos.id = s.id_costumer
     JOIN sales_product sp ON sp.id_sale = s.id
     JOIN products p ON p.id = sp.id_product
     JOIN payment_method pm ON pm.id = s.payment_method
  GROUP BY s.id, cos.id, cos.name, pm.description
  ORDER BY s.date;

ALTER TABLE public.sales_info
    OWNER TO postgres;

-- View: public.view_all_payment_info

-- DROP VIEW public.view_all_payment_info;

CREATE OR REPLACE VIEW public.view_all_payment_info
 AS
 SELECT pay.id AS payment_id,
    c.id AS costumer_id,
    c.name AS costumer_name,
    s.id AS id_sale,
    pay.value,
    pay.date,
    pm.description AS payment_description,
    COALESCE(pay.payer, c.name) AS payer
   FROM payments pay
     JOIN sales s ON s.id = pay.id_sale
     JOIN costumers c ON c.id = s.id_costumer
     JOIN payment_method pm ON pm.id = pay.payment_method;

ALTER TABLE public.view_all_payment_info
    OWNER TO postgres;

