 Paulo Ricken: CREATE TABLE dispositivo (
    id_dispositivo SERIAL PRIMARY KEY,
    nome VARCHAR(255) NOT NULL,
    status VARCHAR(10) CHECK (status IN ('ativo', 'inativo'))
);

CREATE TABLE pergunta (
    id_pergunta SERIAL PRIMARY KEY,
    texto TEXT NOT NULL,
    status VARCHAR(10) CHECK (status IN ('ativa', 'inativa'))
);

CREATE TABLE avaliacao (
    id_avaliacao SERIAL PRIMARY KEY,
    id_setor INT REFERENCES setor(id_setor),
    id_pergunta INT REFERENCES pergunta(id_pergunta),
    id_dispositivo INT REFERENCES dispositivo(id_dispositivo),
    resposta INT CHECK (resposta BETWEEN 0 AND 10) NOT NULL,
    feedback TEXT,
    data_hora TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE setor (
    id_setor SERIAL PRIMARY KEY,
    nome VARCHAR(255) NOT NULL
);

 CREATE TABLE IF NOT EXISTS public.usuario
(
    id integer NOT NULL DEFAULT nextval('usuario_id_seq'::regclass),
    username character varying(50) COLLATE pg_catalog."default" NOT NULL,
    password character varying(255) COLLATE pg_catalog."default" NOT NULL,
    role character varying(20) COLLATE pg_catalog."default" DEFAULT 'user'::character varying,
    CONSTRAINT usuario_pkey PRIMARY KEY (id),
    CONSTRAINT usuario_username_key UNIQUE (username)
)