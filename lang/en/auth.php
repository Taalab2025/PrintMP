<?php
/**
 * Authentication English Translations
 * File path: lang/en/auth.php
 *
 * @package Egypt Printing Services Marketplace
 * @author  Development Team
 */

return [
    // Login
    'login' => 'Login',
    'email' => 'Email',
    'password' => 'Password',
    'remember_me' => 'Remember me',
    'forgot_password' => 'Forgot Password?',
    'no_account' => 'Don\'t have an account?',
    'login_success' => 'You have been logged in successfully',
    'logout_success' => 'You have been logged out successfully',
    'invalid_credentials' => 'Invalid email or password',

    // Register
    'register' => 'Register',
    'account_type' => 'Account Type',
    'customer' => 'Customer',
    'vendor' => 'Vendor (Printing Service)',
    'basic_info' => 'Basic Information',
    'name' => 'Full Name',
    'confirm_password' => 'Confirm Password',
    'password_hint' => 'Minimum 8 characters, at least one letter and one number',
    'email_taken' => 'This email is already in use',
    'registration_success' => 'Registration successful! Please check your email to verify your account',
    'registration_failed' => 'Registration failed. Please try again',

    // Vendor Registration
    'vendor_info' => 'Vendor Information',
    'company_name_en' => 'Company Name (English)',
    'company_name_ar' => 'Company Name (Arabic)',
    'phone' => 'Phone Number',
    'address' => 'Address',
    'vendor_terms_intro' => 'As a vendor, you agree to the following:',
    'vendor_terms_1' => 'You can respond to 10 quote requests per month for free',
    'vendor_terms_2' => 'After 10 requests, you will need to subscribe to a paid plan',
    'vendor_terms_3' => 'Your account will be reviewed and approved before appearing on the platform',

    // Terms
    'agree_terms' => 'I agree to the',
    'terms_link' => 'Terms and Conditions',
    'have_account' => 'Already have an account?',

    // Password Reset
    'forgot_password_instructions' => 'Enter your email address and we will send you a link to reset your password.',
    'send_reset_link' => 'Send Reset Link',
    'back_to_login' => 'Back to Login',
    'reset_link_sent' => 'Password reset link has been sent to your email',
    'reset_failed' => 'Failed to send reset link. Please try again',
    'reset_password' => 'Reset Password',
    'new_password' => 'New Password',
    'password_reset_success' => 'Your password has been reset successfully. You can now login with your new password',

    // Email Verification
    'email_verified' => 'Your email has been verified. You can now login',
    'invalid_token' => 'The token is invalid or has expired',

    // Form Validation
    'required' => 'The :field field is required',
    'email_format' => 'The :field must be a valid email address',
    'min' => 'The :field must be at least :min characters',
    'max' => 'The :field may not be greater than :max characters',
    'matches' => 'The :field must match the :other field',
    'unique' => 'The :field has already been taken',
    'numeric' => 'The :field must be a number',
    'alpha' => 'The :field may only contain letters',
    'alphanumeric' => 'The :field may only contain letters and numbers',
    'url' => 'The :field must be a valid URL',
    'invalid_input' => 'Invalid input. Please check your form and try again',
];
