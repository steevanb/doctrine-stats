#!/usr/bin/env bash

set -eu

if [ -z "${BIN_DIR-}" ]; then
    BIN_DIR="bin"
fi

if [ -z "${DOCKER_IMAGE_NAME-}" ]; then
    DOCKER_IMAGE_NAME="${DOCKER_CI_IMAGE_NAME}"
fi

if [ -z "${I_AM_CORE_DOCKER_CONTAINER:-}" ]; then
    set +e
    tty -s && isInteractiveShell=true || isInteractiveShell=false
    set -e

    if ${isInteractiveShell}; then
        interactiveParameter="--interactive"
    else
        interactiveParameter=
    fi

    readonly coreContractPath=$(realpath "${ROOT_DIR}"/../core-contract)

    docker \
        run \
            --rm \
            --tty \
            ${interactiveParameter} \
            --volume "${ROOT_DIR}":/app \
            --volume ${coreContractPath}:${coreContractPath} \
            --user "$(id -u)":"$(id -g)" \
            --entrypoint "/app/${BIN_DIR}/$(basename "${0}")" \
            --workdir /app \
            --env I_AM_CORE_DOCKER_CONTAINER=true \
            --env HOST_ROOT_DIR="${ROOT_DIR}" \
            "${DOCKER_IMAGE_NAME}" \
            "${@}"

    exit 0
fi
