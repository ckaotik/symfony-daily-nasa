# What this is

This is a test project using Symfony. The goal is to provide a console command that allows donwloading images from the [NASA Open APIs](https://api.nasa.gov/index.html).

# How to use it

1. Createt the docker container  
  `make build`
2. Launch the docker container  
  `make shell`
3. Run the command  
  `bin/console daily-nasa -h`
4. Have fun!

You can optionally provide your own API key. To do so, copy the create an `.env.local` file based on `.env.sample` and set your key there. You can check this by running `php bin/console debug:dotenv`, which should list your `NASA_API_KEY`.

## Examples

- `php bin/console daily-nasa images --date 2022-02-02`
- `php bin/console daily-nasa thumbnails --date 2015-10-31 --imageType thumbs`

# How to develop with it

Q: How do I get a list of defined services?  
A: Run `php bin/console debug:autowiring`