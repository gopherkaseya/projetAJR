-- les note d'un carnet attribué à une antenne ou service et ordonnancées hors de celui-ci ou celle-ci
SELECT  num_note, co1.commune pr_compt_de, co.commune carnet_de -- ,service
, num_debut
FROM `t_note` n
INNER JOIN t_carnet c ON n.num_note
BETWEEN c.num_debut
AND c.num_debut +49
INNER JOIN t_carnet_attribuer ca ON ca.id_carnet = c.id
INNER JOIN t_commune co ON co.id = ca.id_commune
-- inner join t_service s on s.id = ca.id_service
INNER JOIN t_commune co1 ON co1.id = n.pr_cpt_de_id_com
WHERE ca.id_commune != n.pr_cpt_de_id_com
ORDER BY num_debut, num_note