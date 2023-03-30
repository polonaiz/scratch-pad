FROM mcr.microsoft.com/vscode/devcontainers/base:0-focal

RUN apt-get update && \
    apt-get install -y tzdata && \
    ln -fs /usr/share/zoneinfo/Asia/Seoul /etc/localtime && \
    dpkg-reconfigure --frontend noninteractive tzdata

# make
RUN apt-get update && \
    apt-get install -y make vim bash-completion

# docker-ce-cli
# https://docs.docker.com/engine/install/ubuntu/
RUN apt-get update
RUN sudo apt-get install ca-certificates curl gnupg lsb-release
RUN mkdir -p /etc/apt/keyrings
RUN curl -fsSL https://download.docker.com/linux/ubuntu/gpg | gpg --dearmor -o /etc/apt/keyrings/docker.gpg
RUN echo "deb [arch=$(dpkg --print-architecture) signed-by=/etc/apt/keyrings/docker.gpg] https://download.docker.com/linux/ubuntu $(lsb_release -cs) stable" | sudo tee /etc/apt/sources.list.d/docker.list > /dev/null
RUN apt-get update
RUN apt-get install -y docker-ce-cli

# php
RUN apt-get update && \
    apt-get install -y php-cli php-curl php-dom composer
