create table channel(
	real_power double,
	apparent_power double,
	power_factor double,
	vrms double,
	irms double,
	wh_inc double,
	wh double,
	phase int,
        timestamp timestamp default CURRENT_TIMESTAMP
);
