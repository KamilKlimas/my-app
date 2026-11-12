INSERT INTO maps (name, pos_x, pos_y, scale) VALUES
    ('de_ancient', -2953, 2164, 5.00),
    ('de_anubis',  -2796, 3328, 5.22),
    ('de_dust2',   -2476, 3239, 4.40),
    ('de_inferno', -2087, 3870, 4.90),
    ('de_mirage',  -3230, 1713, 5.00),
    ('de_nuke',    -3453, 2887, 7.00)
ON CONFLICT (name) DO NOTHING;

INSERT INTO users (email, name, password_hash, role) VALUES
    ('nightowl@example.com', 'nightowl', '$2y$10$P127awy6qe45oy7UWf.35Oyq/7pb560HHrpA7sec2JXX84svlOSmK', 'USER'),
    ('admin@example.com',    'admin',    '$2y$10$/Kuk7i3bDqr3EibsEhtzFuO4ld60IY9TBRmGb8bOz9fMhFB7SiIcO', 'ADMIN')
ON CONFLICT ((LOWER(email))) DO NOTHING;
