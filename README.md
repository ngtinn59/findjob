# Job Finder Application

## Introduction
This application bridges the gap between job seekers and employers, streamlining the recruitment and job search process. It provides real-time notifications, interactive features, and robust management tools for applicants, employers, and administrators.

---

## Features

### For Job Seekers (Applicants)
- **Login**: Securely access your account.
- **Register**: Create a new account quickly and easily.
- **Forgot Password**: Recover your account via email.
- **Logout**: Securely exit your account when not in use.
- **Create Profile**:
    - Provide personal information, work experience, skills, and education.
- **Create CV**:
    - Build a professional CV directly on the platform.
    - Download or share your CV with employers.
- **Search for Jobs**:
    - Find jobs based on keywords, location, industry, and salary.
- **Filter Jobs**:
    - Narrow down job listings by specific criteria.
- **Job Recommendations**:
    - Get personalized job suggestions based on your profile and preferences.
- **View Job Details**:
    - Access detailed job descriptions, requirements, and benefits.
- **View Company Details**:
    - Learn more about hiring companies, their industry, and size.
- **Save Jobs**:
    - Bookmark favorite jobs for future reference.
- **View Saved Jobs**:
    - Access the list of bookmarked jobs at any time.
- **Apply for Jobs**:
    - Submit applications directly through the platform.
- **Check Application Status**:
    - Track the progress of your job applications (e.g., pending, approved, rejected).
- **Real-Time Application Status Notifications**:
    - Receive instant updates on your application status.
- **Real-Time Chat with Employers**:
    - Communicate directly with employers through the integrated messaging system.

---

### For Employers (Recruiters)
- **Login**: Securely access your account.
- **Register**: Create a new recruiter account.
- **Logout**: Safely exit your account.
- **Forgot Password**: Recover your account if necessary.
- **Change Password**: Update your account password.
- **Update Personal Information**: Keep your profile details up to date.
- **Manage Job Postings**:
    - Create, update, or remove job postings.
    - Track the number of applications per job.
- **Handle Application Status**:
    - Approve, reject, or request additional information from applicants.
- **Real-Time Notifications**:
    - Get notified instantly when an applicant submits a job application.
- **Send Emails to Applicants**:
    - Communicate with applicants via email about their application status.
- **Search for Suitable Candidates**:
    - Find applicants that match your job criteria.
- **Update Company Profile**:
    - Maintain company information and branding.
- **Real-Time Chat with Applicants**:
    - Interact with job seekers through an integrated chat system.

---

### For Admin
- **Real-Time Notifications**:
    - Receive alerts when a recruiter posts a new job.
- **Manage Countries**:
    - Add, update, or remove country records.
- **Manage Company Types**:
    - Handle categories and types of companies.
- **Manage Cities**:
    - Oversee city-level location data.
- **Manage Work Locations**:
    - Administer job location options.
- **Manage Job Postings**:
    - Review and manage all job postings on the platform.
- **Manage User Accounts**:
    - Oversee accounts for both job seekers and recruiters.
- **Statistics**:
    - Generate reports and analyze data on platform usage.
- **Manage Districts**:
    - Control data for smaller administrative regions.
- **Manage Languages**:
    - Add or update supported languages for the platform.
- **Manage Work Formats**:
    - Administer types of job formats (e.g., remote, on-site).
- **Manage Education Levels**:
    - Oversee education requirements for job postings.
- **Manage Job Levels**:
    - Administer career levels for job listings.
- **Manage Experience Levels**:
    - Handle criteria for required years of experience.
- **Manage Companies**:
    - Review and update company information.

---

## Technologies Used
- **Backend**: Laravel API.
- **Database**: MySQL.
- **Real-Time Features**: WebSocket ( Pusher ).
- **CI/CD**: Docker, GitHub Actions.

---

## Installation and Setup
1. **Clone the repository**:
   ```bash
   git clone https://github.com/ngtinn59/findjob.git
   cd findjob
   docker run --rm -v $(pwd):/app composer install
   docker-compose up -d
   cp .env.example .env
   nano .env
   
   DB_CONNECTION=mysql
   DB_HOST=db
   DB_PORT=3306
   DB_DATABASE=laravel
   DB_USERNAME=laraveluser
   DB_PASSWORD=your_laravel_db_password
   
   docker-compose exec app php artisan key:generate
   docker-compose exec app php artisan config:cache
   
   docker-compose exec db bash
   
   mysql -u root -p
   GRANT ALL ON laravel.* TO 'laraveluser'@'%' IDENTIFIED BY 'your_laravel_db_password';
   FLUSH PRIVILEGES;
   EXIT;
   
   docker-compose exec app php artisan migrate:fresh --seed
   
   

   

   
