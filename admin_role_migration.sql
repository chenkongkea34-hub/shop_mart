-- Run once for databases created before the users.role column was added.
ALTER TABLE `users`
  ADD COLUMN `role` VARCHAR(20) NOT NULL DEFAULT 'customer' AFTER `password`;

-- After confirming the account exists, promote it to administrator:
-- UPDATE `users` SET `role` = 'admin' WHERE `email` = 'your-admin-email@example.com';
