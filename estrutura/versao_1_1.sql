drop index if exists saltesdepart_saltes_in;
create unique index saltesdepart_saltes_depart_in on saltesdepart (saltes, depart);