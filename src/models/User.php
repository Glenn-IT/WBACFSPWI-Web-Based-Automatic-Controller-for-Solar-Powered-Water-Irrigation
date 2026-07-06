<?php

class User
{
    public static function findByEmail(string $email): ?array
    {
        $stmt = getDb()->prepare('SELECT * FROM users WHERE email = ? LIMIT 1');
        $stmt->execute([$email]);
        $user = $stmt->fetch();
        return $user ?: null;
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
