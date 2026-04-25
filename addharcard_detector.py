import cv2
import pytesseract
from PIL import Image, ImageDraw, ImageFont
import datetime
import schedule
import time
import pymysql
import os

def job():
    db = {
        'host' : 'localhost',
        'user' : 'root',
        'password' : '',
        'port' : 3308,
        'database' : 'ksrtc',
    }

    conn=pymysql.connect(**db)
    cursor=conn.cursor()


    # Fixing selection query — removing `updated_at` usage
    cursor.execute("""
        SELECT id, `from`, `to` 
        FROM ticket_requests 
        WHERE status = 'initiated' OR status = 'inprogress'
        ORDER BY id ASC 
        LIMIT 1 FOR UPDATE
    """)
    row = cursor.fetchone()

    if row:
        ticket_id, u_start_id, u_stop_id = row

        # Set status to inprogress
        cursor.execute("UPDATE ticket_requests SET status = 'inprogress' WHERE id = %s", (ticket_id,))
        conn.commit()

        # Fetch stop names
        cursor.execute("SELECT LOWER(TRIM(stops)) FROM stops WHERE id = %s", (u_start_id,))
        u_start = cursor.fetchone()[0]

        cursor.execute("SELECT LOWER(TRIM(stops)) FROM stops WHERE id = %s", (u_stop_id,))
        u_stop = cursor.fetchone()[0]

        print(f"Start ID: {u_start_id} ({u_start}), Stop ID: {u_stop_id} ({u_stop})")
    else:
        print("No valid ticket request found.")
        return


    image_folder = 'temp'

    image_files = [f for f in os.listdir(image_folder) if f.lower().endswith(('.jpg', '.png', '.jpeg'))]

    if not image_files:
        print("No images found in 'temp' folder.")
        return

    image_files.sort(key=lambda x: os.path.getmtime(os.path.join(image_folder, x)), reverse=True)

    latest_image = image_files[0]
    image_path = os.path.join(image_folder, latest_image)



    pytesseract.pytesseract.tesseract_cmd=r"C:\Program Files\Tesseract-OCR\tesseract.exe"

    #image_path=r"temp\1747379422_6826e4dee39aa.jpg"

    img=cv2.imread(image_path)

    gray=cv2.cvtColor(img, cv2.COLOR_BGR2GRAY)

    text=pytesseract.image_to_string(gray)

    print('Extracted text:\n',text)

    def fun(text):
        if "FEMALE" in text:
            result="female"
        elif "MALE" in text:
            result="male"
        elif "Male" in text:
            result="male"
        elif "Female" in text:
            result="female"
        else:
            result="Gender not found"
        return result
    result=fun(text)
    print(result)

    global gender
    global locations
    global ticket
    gender=result
    locations=['cbt','old bus stand','hosur cross','kims','vidyanager','unakal','biridevarakoppa','apmc','navanager','iskcon temple','sdm','vidyagiri','jubilee circle']
    start_location={'cbt':0,
                    'old bus stand':1,
                    'hosur cross':2,
                    'kims':3,
                    'vidyanager':4,
                    'unakal':5,
                    'biridevarakoppa':6,
                    'apmc':7,
                    'navanager':8,
                    'iskcon temple':9,
                    'sdm':10,
                    'vidyagiri':11,
                    'jubilee circle':12}
    stop_location={'cbt':5,
                    'old bus stand':5,
                    'hosur cross':5,
                    'kims':5,
                    'vidyanager':5,
                    'unakal':5,
                    'biridevarakoppa':5,
                    'apmc':5,
                    'navanager':5,
                    'iskcon temple':5,
                    'sdm':5,
                    'vidyagiri':5,
                    'jubilee circle':5}

    ticket=0

    def gender_base():
        global gender
        global ticket
        if gender=='female':
            ticket=0
            print('AVALABLE STOPS : 📍CBT\n\t\t 📍OLD BUS STAND\n\t\t 📍HOSUR CROSS\n\t\t 📍KIMS\n\t\t 📍VIDYANAGER\n\t\t 📍UNAKAL\n\t\t 📍BIRIDEVARAKOPPA\n\t\t 📍APMC\n\t\t 📍NAVANAGER\n\t\t 📍ISKCON TEMPLE\n\t\t 📍SDM\n\t\t 📍VIDYAGIRI\n\t\t 📍JUBILEE CIRCLE\n\t\t' )
            start=u_start.lower()
            stop=u_stop.lower()
            if start in start_location and stop in stop_location:
                print(f'PAY {ticket} RS')
                def generate_receipt_image(location, gender, amount, date, status, output_path):
                    img_width, img_height = 800, 600
                    image = Image.new('RGB', (img_width, img_height), color='white')
                    draw = ImageDraw.Draw(image)

                    try:
                        font_title = ImageFont.truetype("arialbd.ttf", 24)  # Bold font
                        font = ImageFont.truetype("arial.ttf", 18)
                    except IOError:
                        font_title = ImageFont.load_default()
                        font = ImageFont.load_default()

                    x = 50
                    y = 30

                    try:
                        logo = Image.open("img/KSRTC-logo.png").convert("RGBA")
                        logo_size = (80, 80)
                        logo = logo.resize(logo_size)

                        # Add white background for transparent logos
                        white_bg = Image.new("RGBA", logo_size, "WHITE")
                        white_logo = Image.alpha_composite(white_bg, logo)

                        image.paste(white_logo.convert("RGB"), (x, y))
                    except Exception as e:
                        print(f"Error loading logo: {e}")
                        draw.text((x, y), "[KSRTC Logo]", font=font, fill='black')

                    # Text lines
                    title_lines = ["KARNATAKA STATE ROAD TRANSPORT", "CORPORATION"]

                    line_spacing = 5
                    text_x = x + logo_size[0] + 20

                    # Calculate vertical position to center both lines with the logo
                    line_heights = []
                    line_widths = []

                    for line in title_lines:
                        bbox = draw.textbbox((0, 0), line, font=font_title)
                        width = bbox[2] - bbox[0]
                        height = bbox[3] - bbox[1]
                        line_widths.append(width)
                        line_heights.append(height)

                    total_text_height = sum(line_heights) + line_spacing
                    text_start_y = y + (logo_size[1] - total_text_height) // 2

                    # Draw both lines
                    for i, line in enumerate(title_lines):
                        draw.text((text_x, text_start_y), line, font=font_title, fill='black')
                        text_start_y += line_heights[i] + line_spacing


                    y += 100  # Move down for rest of the receipt

                    draw.text((x, y), "Receipt", font=font_title, fill='black')
                    y += 40

                    #draw.text((x, y), f"Transaction ID: {transaction_id}", font=font, fill='black'); y += 30
                    draw.text((x, y), f"Location: {location}", font=font, fill='black'); y += 30
                    draw.text((x, y), f"Gender: {gender}", font=font, fill='black'); y += 30
                    draw.text((x, y), f"Amount: {amount:.2f} RS", font=font, fill='black'); y += 30
                    draw.text((x, y), f"Date: {date}", font=font, fill='black'); y += 30
                    draw.text((x, y), f"Status: {status}", font=font, fill='black'); y += 40

                    draw.line([(x, y), (img_width - x, y)], fill='black', width=1); y += 20

                    note = ("Note: Please carry a valid ID during your journey.\n"
                            "For any assistance, contact our 24x7 helpline at 1800-123-4567.")
                    draw.multiline_text((x, y), note, font=font, fill='black')

                    image.save(output_path)
                    print(f"Receipt saved as {output_path}")

                            
                            

                
                #transaction_id = 'TXN123456'
                location = f'"{u_start.upper()}" TO "{u_stop.upper()}"'
                gender = result  # from earlier
                amount = ticket
                date = datetime.datetime.now().strftime("%Y-%m-%d %H:%M:%S")
                status = 'Completed'

                base_name = os.path.splitext(os.path.basename(image_path))[0]

                result_folder = "result"
                os.makedirs(result_folder, exist_ok=True)  # Make sure the result folder exists

                output_path = os.path.join(result_folder, f"{base_name}.jpg")
                generate_receipt_image(location, gender, amount, date, status, output_path)

                # Store the result file path to database, which will also auto-set the status via trigger
                # Update amount in the database
                # After generating the receipt image and setting the result
                cursor.execute("UPDATE ticket_requests SET result = %s, amount = %s, gender = %s, status = 'completed' WHERE id = %s", (output_path, ticket, gender, ticket_id))
                conn.commit()
                



                try:
                    os.remove(image_path)
                    print(f"{image_path} has been deleted.")
                except Exception as e:
                    print(f"Error deleting image: {e}")

            else:
                if start not in start_location: 
                    print(f'YOUR {start} START NOT AVALABLE') 
                elif stop not in stop_location:
                    print(f'YOUR {stop} STOP NOT AVALABLE')
            

        


        elif gender=='male':
            print('AVALABLE STOPS : 📍CBT\n\t\t 📍OLD BUS STAND\n\t\t 📍HOSUR CROSS\n\t\t 📍KIMS\n\t\t 📍VIDYANAGER\n\t\t 📍UNAKAL\n\t\t 📍BIRIDEVARAKOPPA\n\t\t 📍APMC\n\t\t 📍NAVANAGER\n\t\t 📍ISKCON TEMPLE\n\t\t 📍SDM\n\t\t 📍VIDYAGIRI\n\t\t 📍JUBILEE CIRCLE\n\t\t')
            start=u_start.lower()
            stop=u_stop.lower()
            if start in start_location and stop in stop_location:
                if start_location[start] < start_location[stop]:
                    ticket=(start_location[stop]-start_location[start])*stop_location[stop]
                    print(f'PAY {ticket} RS')
                    def generate_receipt_image(location, gender, amount, date, status, output_path):
                        img_width, img_height = 800, 600
                        image = Image.new('RGB', (img_width, img_height), color='white')
                        draw = ImageDraw.Draw(image)

                        try:
                            font_title = ImageFont.truetype("arialbd.ttf", 24)  # Bold font
                            font = ImageFont.truetype("arial.ttf", 18)
                        except IOError:
                            font_title = ImageFont.load_default()
                            font = ImageFont.load_default()

                        x = 50
                        y = 30

                        try:
                            logo = Image.open("img/KSRTC-logo.png").convert("RGBA")
                            logo_size = (80, 80)
                            logo = logo.resize(logo_size)

                            # Add white background for transparent logos
                            white_bg = Image.new("RGBA", logo_size, "WHITE")
                            white_logo = Image.alpha_composite(white_bg, logo)

                            image.paste(white_logo.convert("RGB"), (x, y))
                        except Exception as e:
                            print(f"Error loading logo: {e}")
                            draw.text((x, y), "[KSRTC Logo]", font=font, fill='black')

                        # Text lines
                        title_lines = ["KARNATAKA STATE ROAD TRANSPORT", "CORPORATION"]

                        line_spacing = 5
                        text_x = x + logo_size[0] + 20

                        # Calculate vertical position to center both lines with the logo
                        line_heights = []
                        line_widths = []

                        for line in title_lines:
                            bbox = draw.textbbox((0, 0), line, font=font_title)
                            width = bbox[2] - bbox[0]
                            height = bbox[3] - bbox[1]
                            line_widths.append(width)
                            line_heights.append(height)

                        total_text_height = sum(line_heights) + line_spacing
                        text_start_y = y + (logo_size[1] - total_text_height) // 2

                        # Draw both lines
                        for i, line in enumerate(title_lines):
                            draw.text((text_x, text_start_y), line, font=font_title, fill='black')
                            text_start_y += line_heights[i] + line_spacing


                        y += 100  # Move down for rest of the receipt

                        draw.text((x, y), "Receipt", font=font_title, fill='black')
                        y += 40

                        #draw.text((x, y), f"Transaction ID: {transaction_id}", font=font, fill='black'); y += 30
                        draw.text((x, y), f"Location: {location}", font=font, fill='black'); y += 30
                        draw.text((x, y), f"Gender: {gender}", font=font, fill='black'); y += 30
                        draw.text((x, y), f"Amount: {amount:.2f} RS", font=font, fill='black'); y += 30
                        draw.text((x, y), f"Date: {date}", font=font, fill='black'); y += 30
                        draw.text((x, y), f"Status: {status}", font=font, fill='black'); y += 40

                        draw.line([(x, y), (img_width - x, y)], fill='black', width=1); y += 20

                        note = ("Note: Please carry a valid ID during your journey.\n"
                                "For any assistance, contact our 24x7 helpline at 1800-123-4567.")
                        draw.multiline_text((x, y), note, font=font, fill='black')

                        image.save(output_path)
                        print(f"Receipt saved as {output_path}")

                                
                                

                    
                    #transaction_id = 'TXN123456'
                    location = f'"{u_start.upper()}" TO "{u_stop.upper()}"'
                    gender = result  # from earlier
                    amount = ticket
                    date = datetime.datetime.now().strftime("%Y-%m-%d %H:%M:%S")
                    status = 'Completed'

                    base_name = os.path.splitext(os.path.basename(image_path))[0]

                    result_folder = "result"
                    os.makedirs(result_folder, exist_ok=True)  # Make sure the result folder exists

                    output_path = os.path.join(result_folder, f"{base_name}.jpg")
                    generate_receipt_image(location, gender, amount, date, status, output_path)

                    # Store the result file path to database, which will also auto-set the status via trigger
                    # After generating the receipt image and setting the result
                    cursor.execute("UPDATE ticket_requests SET result = %s, amount = %s, gender = %s, status = 'completed' WHERE id = %s", (output_path, ticket, gender, ticket_id))
                    conn.commit()



                    try:
                        os.remove(image_path)
                        print(f"{image_path} has been deleted.")
                    except Exception as e:
                        print(f"Error deleting image: {e}")
                    
                        
                else:
                    if start_location[stop]<start_location[start]:
                        ticket=(start_location[start]-start_location[stop])*stop_location[stop]
                        print(f'PAY {ticket} RS')
                    def generate_receipt_image(location, gender, amount, date, status, output_path):
                        img_width, img_height = 800, 600
                        image = Image.new('RGB', (img_width, img_height), color='white')
                        draw = ImageDraw.Draw(image)

                        try:
                            font_title = ImageFont.truetype("arialbd.ttf", 24)  # Bold font
                            font = ImageFont.truetype("arial.ttf", 18)
                        except IOError:
                            font_title = ImageFont.load_default()
                            font = ImageFont.load_default()

                        x = 50
                        y = 30

                        try:
                            logo = Image.open("img/KSRTC-logo.png").convert("RGBA")
                            logo_size = (80, 80)
                            logo = logo.resize(logo_size)

                            # Add white background for transparent logos
                            white_bg = Image.new("RGBA", logo_size, "WHITE")
                            white_logo = Image.alpha_composite(white_bg, logo)

                            image.paste(white_logo.convert("RGB"), (x, y))
                        except Exception as e:
                            print(f"Error loading logo: {e}")
                            draw.text((x, y), "[KSRTC Logo]", font=font, fill='black')

                        # Text lines
                        title_lines = ["KARNATAKA STATE ROAD TRANSPORT", "CORPORATION"]

                        line_spacing = 5
                        text_x = x + logo_size[0] + 20

                        # Calculate vertical position to center both lines with the logo
                        line_heights = []
                        line_widths = []

                        for line in title_lines:
                            bbox = draw.textbbox((0, 0), line, font=font_title)
                            width = bbox[2] - bbox[0]
                            height = bbox[3] - bbox[1]
                            line_widths.append(width)
                            line_heights.append(height)

                        total_text_height = sum(line_heights) + line_spacing
                        text_start_y = y + (logo_size[1] - total_text_height) // 2

                        # Draw both lines
                        for i, line in enumerate(title_lines):
                            draw.text((text_x, text_start_y), line, font=font_title, fill='black')
                            text_start_y += line_heights[i] + line_spacing


                        y += 100  # Move down for rest of the receipt

                        draw.text((x, y), "Receipt", font=font_title, fill='black')
                        y += 40

                        #draw.text((x, y), f"Transaction ID: {transaction_id}", font=font, fill='black'); y += 30
                        draw.text((x, y), f"Location: {location}", font=font, fill='black'); y += 30
                        draw.text((x, y), f"Gender: {gender}", font=font, fill='black'); y += 30
                        draw.text((x, y), f"Amount: {amount:.2f} RS", font=font, fill='black'); y += 30
                        draw.text((x, y), f"Date: {date}", font=font, fill='black'); y += 30
                        draw.text((x, y), f"Status: {status}", font=font, fill='black'); y += 40

                        draw.line([(x, y), (img_width - x, y)], fill='black', width=1); y += 20

                        note = ("Note: Please carry a valid ID during your journey.\n"
                                "For any assistance, contact our 24x7 helpline at 1800-123-4567.")
                        draw.multiline_text((x, y), note, font=font, fill='black')

                        image.save(output_path)
                        print(f"Receipt saved as {output_path}")

                                
                                

                    
                    #transaction_id = 'TXN123456'
                    location = f'"{u_start.upper()}" TO "{u_stop.upper()}"'
                    gender = result  # from earlier
                    amount = ticket
                    date = datetime.datetime.now().strftime("%Y-%m-%d %H:%M:%S")
                    status = 'Completed'

                    base_name = os.path.splitext(os.path.basename(image_path))[0]

                    result_folder = "result"
                    os.makedirs(result_folder, exist_ok=True)  # Make sure the result folder exists

                    output_path = os.path.join(result_folder, f"{base_name}.jpg")
                    generate_receipt_image(location, gender, amount, date, status, output_path)

                    # Store the result file path to database, which will also auto-set the status via trigger
                    # After generating the receipt image
                    # After generating the receipt image and setting the result
                    cursor.execute("UPDATE ticket_requests SET result = %s, amount = %s, gender = %s, status = 'completed' WHERE id = %s", (output_path, ticket, gender, ticket_id))
                    conn.commit()



                    try:
                        os.remove(image_path)
                        print(f"{image_path} has been deleted.")
                    except Exception as e:
                        print(f"Error deleting image: {e}")

            else: 
                if start not in start_location:
                    print(f'YOUR {start} START NOT AVAILBLE')   
                elif stop not in stop_location:
                    print(f'YOUR {stop} STOP NOT AVAILABLE')
                
        else:
            print('ENTER CORRECT GENDER')
    gender_base()
    cursor.close()
    conn.close()
    

schedule.every(3).seconds.do(job)

while True:
    print("Scheduler is checking jobs...")
    schedule.run_pending()
    time.sleep(1)
