# 🌐 Web-Based E-Commerce Crawler

## 🚀 Overview

This project is a web-based application that consists of a backend solution built with PHP and a frontend built with plain JavaScript. The application is designed to perform web crawling on selected e-commerce homepages and display the obtained results on dashboards.

## 🔍 Features

### Backend (API)

- **Web Crawling**: The API has an endpoint that performs web crawling on specified e-commerce homepage URLs listed in a text file.
- **Data Extraction**: The API analyzes the page structure and content, extracting details such as:
  - Product lists
  - Prices
  - Discounts
  - Categories
- **Security**: Implements user authentication and API key verification for secure access.
- **Response Format**: Provides results in JSON format for easy visualization on the frontend.

### Frontend (Dashboard)

- **Data Visualization**: A simple dashboard that displays API responses.
- **Search Functionality**: Users can trigger API requests and view obtained data through a search feature.
- **Graphs and Charts**: Displays various visualizations such as:
  - Product category representation (e.g., pie charts)
  - Price distribution (e.g., bar charts)
  - Discount prevalence (e.g., line charts)
  - Popularity trends (e.g., line or scatter plots, if data is available)

## 📋 Requirements

- PHP 7.4 or higher
- Composer for dependency management
- GuzzleHTTP for making HTTP requests
- Symfony DomCrawler for HTML parsing
- A web server (e.g., Apache, Nginx)

## ⚙️ Installation

1. Clone the repository:
   ```bash
   git clone <repository-url>
   cd <repository-directory>

    Install the required dependencies:

    bash

    composer install

    Create a urls.txt file in the project-directory and add the e-commerce homepage URLs you want to crawl.

    Configure your web server to serve the application.

## 🏁 Usage

- Start your web server and access the API endpoint.
- Use the frontend dashboard to input search criteria and view the results.
- The API will crawl the specified URLs and return structured data for visualization.

## 🔒 Security

Ensure to implement proper security measures, including API key management and user authentication, to protect your application from unauthorized access.
Optional Features

- Implement functionality to extract and display:
  - Discounts and promotions
  - Advertisements and campaigns

## 🌟 Future Improvements

- Real-time crawling and scraping updates on the frontend.
- Enhanced data analysis and visualization features.

## 🤝 Contributing

Contributions are welcome! If you have suggestions or improvements, feel free to open an issue or submit a pull request.
License

Happy Crawling! 🚀


### Key Changes Made:
- **Section Headers**: Used emojis to make sections stand out.
- **Formatting**: Improved structure and readability with horizontal lines and bullet points.
- **Introduction**: Added a welcoming note to engage readers.
- **Clear Instructions**: Made installation and usage instructions clearer.
- **Optional Features & Future Improvements**: Separated these for emphasis.
