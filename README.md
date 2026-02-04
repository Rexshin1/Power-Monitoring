# ⚡ Power Monitoring Dashboard

A comprehensive web-based dashboard for real-time power monitoring and management, built with **Laravel** and **Now UI Dashboard**. This system provides analytics, reporting, and automated notifications via WhatsApp.

![Laravel](https://img.shields.io/badge/Laravel-10.x-FF2D20?style=for-the-badge&logo=laravel&logoColor=white)
![Node.js](https://img.shields.io/badge/Node.js-18.x-339933?style=for-the-badge&logo=nodedotjs&logoColor=white)
![MySQL](https://img.shields.io/badge/MySQL-8.0-4479A1?style=for-the-badge&logo=mysql&logoColor=white)

## 🚀 Features

-   **Real-time Monitoring**: Visualize power usage and source status in real-time.
-   **Interactive Dashboard**: Beautiful UI based on Now UI Dashboard for easy data visualization.
-   **Device Management**: Manage power sources, buildings, and sensors effectively.
-   **WhatsApp Notifications**: Integrated gateway (`wa-gateway`) for instant alerts and reporting.
-   **Data Simulation**: Includes `simulate_device.ps1` for testing and development purposes.
-   **User Management**: Role-based access control for administrators and users.

## 🛠️ Technology Stack

-   **Backend**: Laravel 10 (PHP)
-   **Frontend**: Blade Templates, Javascript, Now UI Dashboard
-   **Database**: MySQL
-   **Notification Service**: Node.js (WhatsApp Gateway)
-   **Development Tools**: PowerShell (for device simulation)

## 📦 Installation

1.  **Clone the repository**
    ```bash
    git clone https://github.com/yourusername/power-monitoring.git
    cd power-monitoring
    ```

2.  **Install PHP Dependencies**
    ```bash
    composer install
    ```

3.  **Install Node.js Dependencies**
    ```bash
    npm install
    ```

4.  **Environment Setup**
    Copy the `.env.example` file to `.env` and configure your database and other settings.
    ```bash
    cp .env.example .env
    php artisan key:generate
    ```

5.  **Run Migrations**
    ```bash
    php artisan migrate
    ```

6.  **WhatsApp Gateway Setup**
    Navigate to the `wa-gateway` directory and install dependencies.
    ```bash
    cd wa-gateway
    npm install
    ```

## 🖥️ Usage

-   **Start the Laravel Development Server**:
    ```bash
    php artisan serve
    ```

-   **Start the WhatsApp Gateway**:
    ```bash
    cd wa-gateway
    node server.js
    ```
    *(Check standard output for QR code login)*

-   **Simulate Device Data** (Optional):
    ```powershell
    ./simulate_device.ps1
    ```

## 📄 License

This project is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).
