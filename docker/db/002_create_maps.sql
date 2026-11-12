CREATE TABLE maps (
    id               BIGINT GENERATED ALWAYS AS IDENTITY PRIMARY KEY,
    name             VARCHAR(64)      NOT NULL UNIQUE,
    radar_image_path TEXT,
    pos_x            DOUBLE PRECISION NOT NULL,
    pos_y            DOUBLE PRECISION NOT NULL,
    scale            DOUBLE PRECISION NOT NULL CHECK (scale > 0),
    created_at       TIMESTAMPTZ      NOT NULL DEFAULT now()
);
