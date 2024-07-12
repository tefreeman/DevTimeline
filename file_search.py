import os
import re
import argparse
from concurrent.futures import ProcessPoolExecutor, as_completed

def read_file(file_path):
    encodings = ['utf-8', 'iso-8859-1', 'windows-1252']
    for encoding in encodings:
        try:
            with open(file_path, 'r', encoding=encoding) as f:
                return f.read()
        except UnicodeDecodeError:
            continue
    return None  # If all encodings fail, return None

def search_passwords(file_path, passwords):
    content = read_file(file_path)
    if content is None:
        return None

    found_passwords = []
    for password in passwords:
        if re.search(password, content, re.IGNORECASE):
            found_passwords.append(password)

    if found_passwords:
        return file_path, found_passwords
    return None

def process_file(args):
    return search_passwords(*args)

def main():
    parser = argparse.ArgumentParser(description="Search for passwords in a local folder")
    parser.add_argument("folder_path", help="Path to the folder to scan")
    parser.add_argument("--passwords_file", default="passwords.txt", help="File containing passwords to search for")
    parser.add_argument("--output", default="local_password_findings.txt", help="Output file for results")
    args = parser.parse_args()

    # Read passwords from file
    with open(args.passwords_file, 'r') as f:
        passwords = f.read().splitlines()

    # Collect all files in the folder and its subfolders
    files_to_scan = []
    for root, _, files in os.walk(args.folder_path):
        for file in files:
            files_to_scan.append(os.path.join(root, file))

    # Use ProcessPoolExecutor for parallel processing
    with ProcessPoolExecutor() as executor:
        futures = [executor.submit(process_file, (file, passwords)) for file in files_to_scan]

        # Process results and write to output file
        with open(args.output, 'w') as output_file:
            for future in as_completed(futures):
                result = future.result()
                if result:
                    file_path, found_passwords = result
                    relative_path = os.path.relpath(file_path, args.folder_path)
                    print(f"Passwords found in {relative_path}:")
                    output_file.write(f"Passwords found in {relative_path}:\n")
                    for password in found_passwords:
                        print(f"- {password}")
                        output_file.write(f"- {password}\n")
                    print("---")
                    output_file.write("---\n")

    print(f"Scan complete. Results have been written to {args.output}")

if __name__ == "__main__":
    main()
