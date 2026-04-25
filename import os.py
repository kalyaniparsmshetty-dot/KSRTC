import os

# Folder where images are stored
image_folder = ''

# Get list of image files (e.g., .jpg, .png)
image_files = [f for f in os.listdir(image_folder) if f.lower().endswith(('.jpg', '.jpeg', '.png'))]

if not image_files:
    print("No images found in 'temp' folder.")
    exit()

# Sort by modified time (latest image first)
image_files.sort(key=lambda x: os.path.getmtime(os.path.join(image_folder, x)), reverse=True)

# Take the latest image
latest_image = image_files[0]
image_path = os.path.join(image_folder, latest_image)

print(f"Using image: {image_path}")
