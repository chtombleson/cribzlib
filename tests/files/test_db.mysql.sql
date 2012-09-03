use cribzlib_test;
DROP TABLE IF EXISTS test;
CREATE TABLE test (
    id int not null auto_increment,
    name varchar(255) not null,
    value text not null,
    created int not null default 0,
    modified int not null default 0,
    primary key(id)
);
