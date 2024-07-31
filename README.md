Azure AI Services Example
=========================

This repository contains examples of how to use Azure AI services in PHP.

**Included Services:**
- [Azure AI Vision](https://azure.microsoft.com/en-us/products/ai-services/ai-vision/)

Setup
-----

1. Set up the repository by running the following commands:
    ```bash
    git clone git@github.com:chr-hertel/ai-services.git
    cd ai-services
    composer install
    ```
1. Create a `.env.local` file in the root of the project by copying the `.env` and configuring the values:
    ```bash
    cp .env .env.local
    ```
1. Run the example scripts:
    ```bash
    php examples example:ai-vision
    ```

Image Credits
-------------

- data/image01.jpg: https://www.pexels.com/@artempodrez/
- data/image02.jpg: https://www.pexels.com/@kateryna-babaieva-1423213/
- data/image03.jpg: https://www.pexels.com/@ono-kosuki/
- data/image04.jpg: https://www.pexels.com/@emmages/
- data/image05.jpg: https://www.pexels.com/@kindelmedia/
- data/image06.jpg: https://www.pexels.com/@shvetsa/
