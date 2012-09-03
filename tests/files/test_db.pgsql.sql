DROP TABLE IF EXISTS test;
DROP SEQUENCE IF EXISTS test_id_seq;
CREATE SEQUENCE test_id_seq;
CREATE TABLE test (
    id int not null default nextval('test_id_seq') primary key,
    name varchar not null,
    value text not null,
    created int not null default 0,
    modified int not null default 0
);
