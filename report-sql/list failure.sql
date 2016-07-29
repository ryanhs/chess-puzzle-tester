SELECT
    e.name as engine_name,
    p.name as puzzle_name,
    p.answer,
    ep.answer_depth_plus_0,
    ep.answer_depth_plus_1,
    ep.answer_depth_plus_2,
    ep.answer_depth_plus_3
FROM puzzles p
JOIN engine_puzzle ep ON ep.puzzle = p.id
JOIN engines e ON ep.engine = e.id
WHERE
    NOT (
        ep.answer_depth_plus_0 = p.answer
        AND ep.answer_depth_plus_1 = p.answer
        AND ep.answer_depth_plus_2 = p.answer
        AND ep.answer_depth_plus_3 = p.answer
    )
    -- AND e.id = 1 -- stockfish

ORDER BY e.id ASC, p.id ASC
