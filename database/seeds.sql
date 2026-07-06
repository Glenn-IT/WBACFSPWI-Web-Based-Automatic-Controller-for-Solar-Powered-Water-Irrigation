-- Default super admin account.
-- Email: admin@wbacfspwi.local
-- Password: Admin@123  (change immediately after first login)

INSERT INTO users (name, email, password_hash, role, is_active)
VALUES (
    'System Administrator',
    'admin@wbacfspwi.local',
    '$2y$10$HIXLv3XqtJ5g77yPcqX53.HRevEFog6yuVokvgVlU1pKctwGn6B5q',
    'super_admin',
    1
);
