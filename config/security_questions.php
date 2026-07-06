<?php

// Shared list of predefined security questions used by Profile (set/update)
// and the forgot-password recovery flow. Keyed so the DB stores a stable
// short key rather than the full question text.
return [
    'first_pet' => 'What was the name of your first pet?',
    'birth_city' => 'In what city were you born?',
    'mother_maiden' => "What is your mother's maiden name?",
    'childhood_friend' => 'What was the name of your childhood best friend?',
    'favorite_teacher' => 'Who was your favorite teacher?',
];
