Azure AI Services Example
=========================

This repository contains examples of how to use Azure AI services in PHP.

**Included Services:**
- [Azure AI Vision](https://azure.microsoft.com/en-us/products/ai-services/ai-vision/)
- [Azure Text Analytics](https://azure.microsoft.com/en-us/products/ai-services/text-analytics/)
- [Custom Text Classification](https://learn.microsoft.com/en-us/azure/ai-services/language-service/custom-text-classification/overview)

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
   ```dotenv
   # Needed for AI Vision and Sentiment Analysis
   COGNITIVE_SERVICES_ENDPOINT=
   COGNITIVE_SERVICES_KEY=
   ```
1. Running examples scripts, see below.

Examples
--------

### Azure AI Vision

With this example, you can analyze JPG images stored in `data/images/` folder using the Azure AI Vision service.
Just execute the command, select an image and the analyzing feature of the Azure AI Vision service.

```bash
php examples example:ai-vision
```

More information about the Azure AI Vision service can be found [here](https://azure.microsoft.com/en-us/products/ai-services/ai-vision/).

### Azure Sentiment Analysis

This example demonstrates how to use the Azure Text Analytics service to analyze the sentiment of a given text stored
in `data/comments/` folder.

```bash
php examples example:sentiment-analysis
```

More information about the Azure Text Analytics service can be found [here](https://azure.microsoft.com/en-us/products/ai-services/ai-language).

### Azure Custom Language Classification

This is a multistep example, that needs interaction in Azure, see this [Video Tutorial](https://www.youtube.com/watch?v=QK_u2dDHStM).
As a basis we're using 4250 IMDB movie reviews, which are already downloaded, transformed and mapped - step 1 and 2 are already done.

1. Download IMDB movie reviews as json to `data/reviews/raw/`
    ```bash
    php examples example:text-classification:download
    ```
1. Transform the data into txt
    ```bash
    php examples example:text-classification:transform
    ```

Follow the given steps in the video tutorial to create a new Azure Custom Language Classification service and train
it with the transformed data from step 2, store in `data/reviews/txt/` with the class mapping in
`data/reviews/label_classification.json`.

Afterward configure the additional environment variables in your `.env.local` file:

```dotenv
# Needed for Custom Text Classification
TEXT_CLASSIFICATION_PROJECT=
TEXT_CLASSIFICATION_CONTAINER=
TEXT_CLASSIFICATION_ENDPOINT=
TEXT_CLASSIFICATION_KEY=
TEXT_CLASSIFICATION_DEPLOYMENT=
```

Now you can run the classification example:

```bash
php examples example:text-classification:test
```

Image Credits
-------------

- data/image01.jpg: https://www.pexels.com/@artempodrez/
- data/image02.jpg: https://www.pexels.com/@kateryna-babaieva-1423213/
- data/image03.jpg: https://www.pexels.com/@ono-kosuki/
- data/image04.jpg: https://www.pexels.com/@emmages/
- data/image05.jpg: https://www.pexels.com/@kindelmedia/
- data/image06.jpg: https://www.pexels.com/@shvetsa/
