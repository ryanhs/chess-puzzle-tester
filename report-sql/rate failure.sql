SELECT
    from_db.*,
    (from_db.failure_rate / from_db.`from` * 100) as `percentage`

FROM (
    SELECT
        e.name,
        e.elo,
        (
            SELECT
                count(*)
            FROM puzzles p
            JOIN engine_puzzle ep ON ep.puzzle = p.id
            WHERE
                NOT (
                    ep.answer_depth_plus_0 = p.answer
                    AND ep.answer_depth_plus_1 = p.answer
                    AND ep.answer_depth_plus_2 = p.answer
                    AND ep.answer_depth_plus_3 = p.answer
                )
                AND ep.engine = e.id
        ) as failure_rate,
        (
            SELECT count(*) FROM `puzzles`
        ) as `from`

    FROM engines as e
    WHERE e.use = 'y'
    ORDER BY e.elo DESC
) from_db
