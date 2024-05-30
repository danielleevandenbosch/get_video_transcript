import whisper
import ffmpeg
import sys
import os
import uuid

def log_message(message):
    log_file = "/tmp/transcribe_debug.log"
    with open(log_file, "a") as f:
        f.write(message + "\n")

def extract_audio(video_file_path, audio_file_path):
    log_message(f"Extracting audio from {video_file_path} to {audio_file_path}")
    ffmpeg.input(video_file_path).output(audio_file_path).run()

def transcribe_video(video_file_path):
    unique_id = str(uuid.uuid4())
    audio_file_path = f"/tmp/audio_{unique_id}.wav"
    
    log_message(f"Starting transcription for {video_file_path}")
    extract_audio(video_file_path, audio_file_path)
    
    model = whisper.load_model("base")
    result = model.transcribe(audio_file_path)
    
    def format_timestamp(seconds):
        hours = int(seconds // 3600)
        minutes = int((seconds % 3600) // 60)
        seconds = int(seconds % 60)
        return f"{hours:02}:{minutes:02}:{seconds:02}"

    formatted_transcript = ""
    for segment in result['segments']:
        start_time = format_timestamp(segment['start'])
        end_time = format_timestamp(segment['end'])
        text = segment['text'].strip()
        formatted_transcript += f"[{start_time} - {end_time}] {text}\n\n"
    
    os.remove(audio_file_path)
    log_message(f"Finished transcription for {video_file_path}")

    return formatted_transcript

if __name__ == "__main__":
    try:
        video_file_path = sys.argv[1]
        log_message(f"Processing file: {video_file_path}")
        transcript = transcribe_video(video_file_path)
        transcript_file_path = video_file_path + ".txt"
        with open(transcript_file_path, "w") as f:
            f.write(transcript)
        log_message(f"Transcript saved to: {transcript_file_path}")
        print(transcript_file_path)
    except Exception as e:
        log_message(f"Error: {e}")
        print(f"Error: {e}", file=sys.stderr)
        sys.exit(1)