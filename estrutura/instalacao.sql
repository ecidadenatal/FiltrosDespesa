create table saltesdepart (
    sequencial serial not null primary key,
    saltes integer  not null,
    depart integer  not null
  );

create unique index saltesdepart_saltes_in on saltesdepart (saltes);
create index saltesdepart_depart_in on saltesdepart (depart);