CREATE TABLE test (
    id integer not null primary key,
    name varchar not null,
    value text not null,
    created int not null default 0,
    modified int not null default 0
);
