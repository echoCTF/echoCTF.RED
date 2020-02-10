-- #USE echoCTF;

select memc_servers_set('127.0.0.1') INTO @memc_server_set_status;

SELECT memc_set(CONCAT('player:',id),id) FROM player;

-- Populate player_type:PLAYER_ID => player_type
SELECT memc_set(CONCAT('player_type:',id),`type`) FROM player;

-- Populate team_player
SELECT memc_set(CONCAT('team_player:',player_id),team_id) FROM team_player;

-- populate team_finding
SELECT memc_set(CONCAT('team_finding:',t2.team_id, ':', t1.finding_id),t1.player_id) FROM player_finding AS t1 LEFT JOIN team_player AS t2 ON t2.player_id=t1.player_id;

-- populate player_finding
SELECT memc_set(CONCAT('player_finding:',player_id, ':', finding_id),player_id) FROM player_finding;

-- Populate target:ip => target_id
-- target:id => ip
SELECT memc_set(CONCAT('target:',ip),id) FROM target;
SELECT memc_set(CONCAT('target:',id),ip) FROM target;

-- Populate sysconfig
SELECT memc_set(CONCAT('sysconfig:',id),val) FROM sysconfig;

-- populate findings
SELECT memc_set(CONCAT('finding:',protocol,':',ifnull(port,0), ':', target_id ),id) FROM finding;
