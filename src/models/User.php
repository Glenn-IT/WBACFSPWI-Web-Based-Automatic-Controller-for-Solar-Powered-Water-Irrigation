<?php

class User
{
    public static function findByEmail(string $email): ?array
    {
        $stmt = getDb()->prepare('SELECT * FROM users WHERE LOWER(email) = LOWER(?) LIMIT 1');
        $stmt->execute([trim($email)]);
        $user = $stmt->fetch();
        return $user ?: null;
    }

    public static function findByName(string $name): ?array
    {
        $stmt = getDb()->prepare('SELECT * FROM users WHERE LOWER(name) = LOWER(?) LIMIT 1');
        $stmt->execute([trim($name)]);
        $user = $stmt->fetch();
        return $user ?: null;
    }

    public static function isEmailTaken(string $email, ?int $excludeId = null): bool
    {
        if ($excludeId !== null) {
            $stmt = getDb()->prepare('SELECT COUNT(*) FROM users WHERE LOWER(email) = LOWER(?) AND id != ?');
            $stmt->execute([trim($email), $excludeId]);
        } else {
            $stmt = getDb()->prepare('SELECT COUNT(*) FROM users WHERE LOWER(email) = LOWER(?)');
            $stmt->execute([trim($email)]);
        }
        return (int) $stmt->fetchColumn() > 0;
    }

    public static function isNameTaken(string $name, ?int $excludeId = null): bool
    {
        if ($excludeId !== null) {
            $stmt = getDb()->prepare('SELECT COUNT(*) FROM users WHERE LOWER(name) = LOWER(?) AND id != ?');
            $stmt->execute([trim($name), $excludeId]);
        } else {
            $stmt = getDb()->prepare('SELECT COUNT(*) FROM users WHERE LOWER(name) = LOWER(?)');
            $stmt->execute([trim($name)]);
        }
        return (int) $stmt->fetchColumn() > 0;
    }

    public static function countSuperAdmins(?int $excludeId = null): int
    {
        if ($excludeId !== null) {
            $stmt = getDb()->prepare("SELECT COUNT(*) FROM users WHERE role = 'super_admin' AND id != ?");
            $stmt->execute([$excludeId]);
        } else {
            $stmt = getDb()->query("SELECT COUNT(*) FROM users WHERE role = 'super_admin'");
        }
        return (int) $stmt->fetchColumn();
    }

    public static function findById(int $id): ?array
    {
        $stmt = getDb()->prepare('SELECT * FROM users WHERE id = ? LIMIT 1');
        $stmt->execute([$id]);
        $user = $stmt->fetch();
        return $user ?: null;
    }

    public static function updateLastLogin(int $id): void
    {
        $stmt = getDb()->prepare('UPDATE users SET last_login_at = NOW() WHERE id = ?');
        $stmt->execute([$id]);
    }

    public static function updateProfile(int $id, string $name, string $email): void
    {
        $stmt = getDb()->prepare('UPDATE users SET name = ?, email = ? WHERE id = ?');
        $stmt->execute([$name, $email, $id]);
    }

    public static function updatePassword(int $id, string $passwordHash): void
    {
        $stmt = getDb()->prepare('UPDATE users SET password_hash = ? WHERE id = ?');
        $stmt->execute([$passwordHash, $id]);
    }

    public static function all(): array
    {
        $stmt = getDb()->query('SELECT id, name, email, role, is_active, last_login_at, created_at FROM users ORDER BY name ASC');
        return $stmt->fetchAll();
    }

    public static function create(string $name, string $email, string $passwordHash, string $role): int
    {
        $stmt = getDb()->prepare(
            'INSERT INTO users (name, email, password_hash, role) VALUES (?, ?, ?, ?)'
        );
        $stmt->execute([$name, $email, $passwordHash, $role]);
        return (int) getDb()->lastInsertId();
    }

    public static function updateRole(int $id, string $role): void
    {
        $stmt = getDb()->prepare('UPDATE users SET role = ? WHERE id = ?');
        $stmt->execute([$role, $id]);
    }

    public static function setActive(int $id, bool $isActive): void
    {
        $stmt = getDb()->prepare('UPDATE users SET is_active = ? WHERE id = ?');
        $stmt->execute([$isActive ? 1 : 0, $id]);
    }

    public static function delete(int $id): void
    {
        $stmt = getDb()->prepare('DELETE FROM users WHERE id = ?');
        $stmt->execute([$id]);
    }

    // $answer is normalized (trimmed/lowercased) before hashing so verification isn't case-sensitive.
    public static function updateSecurityQuestion(int $id, string $question, string $answer): void
    {
        $hash = password_hash(mb_strtolower(trim($answer)), PASSWORD_DEFAULT);
        $stmt = getDb()->prepare('UPDATE users SET security_question = ?, security_answer_hash = ? WHERE id = ?');
        $stmt->execute([$question, $hash, $id]);
    }

    public static function verifySecurityAnswer(int $id, string $answer): bool
    {
        $stmt = getDb()->prepare('SELECT security_answer_hash FROM users WHERE id = ?');
        $stmt->execute([$id]);
        $row = $stmt->fetch();

        if (!$row || !$row['security_answer_hash']) {
            return false;
        }

        return password_verify(mb_strtolower(trim($answer)), $row['security_answer_hash']);
    }
}
